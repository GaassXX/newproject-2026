//+------------------------------------------------------------------+
//| EA: HTTP Signal Listener for MT5                                 |
//| - Polls an HTTP endpoint for the next trading signal              |
//| - Executes market order with TP and SL                           |
//| Usage: add the EA to a chart, set `SignalUrl` and allow WebRequest|
//+------------------------------------------------------------------+
#property copyright ""
#property link      ""
#property version   "1.0"
#property strict

input string SignalUrl = "http://your-server-host/api/signals/next"; // set to your Laravel API URL (keep /next here)
input string CallbackBase = "http://your-server-host/api/signals"; // used to POST execution results: {CallbackBase}/{id}/executed
input string ApiToken = ""; // set to same value as EA_API_TOKEN in .env
input int    PollIntervalSeconds = 5; // how often to poll
input ulong  MagicNumber = 123456;

#include <Trade\Trade.mqh>
CTrade trade;

int OnInit()
{
   EventSetTimer(PollIntervalSeconds);
   Print("EA initialized. Polling: ", SignalUrl);
   return(INIT_SUCCEEDED);
}

void OnDeinit(const int reason)
{
   EventKillTimer();
}

void OnTimer()
{
   // Perform HTTP GET to fetch the next pending signal
   uchar result[];
   string response_headers;
   string headers = "";
    int res = WebRequest("GET", SignalUrl, headers, 0, NULL, 0, result, response_headers);
   if(res == -1)
   {
       Print("WebRequest failed. Error: ", GetLastError());
       ResetLastError();
       return;
   }

   string body = CharArrayToString(result);
   if(StringLen(body) == 0)
   {
       // nothing to do
       return;
   }

   // Simple check for 'no signal' or empty
   if(StringFind(body, 'no signal') >= 0 || StringFind(body, '204') >= 0)
       return;

   // Parse JSON naively (expects keys: instrument, side, volume, take_profit, stop_loss)
    string instrument = JsonGetString(body, "instrument");
   string side = JsonGetString(body, "side");
   double volume = StrToDouble(JsonGetString(body, "volume"));
   double tp = StrToDouble(JsonGetString(body, "take_profit"));
   double sl = StrToDouble(JsonGetString(body, "stop_loss"));
    string id = JsonGetString(body, "id");

   if(StringLen(instrument) == 0 || StringLen(side) == 0)
   {
       Print("Invalid signal payload: ", body);
       return;
   }

   // Convert instrument naming (EUR_USD or EUR-USD) -> EURUSD
   string symbol = StringReplace(instrument, "_", "");
   symbol = StringReplace(symbol, "-", "");
   symbol = StringReplace(symbol, "/", "");

   if(!SymbolInfoTick(symbol, NULL))
   {
       Print("Symbol not available on this account: ", symbol);
       return;
   }

   trade.SetExpertMagicNumber(MagicNumber);

   bool ok = false;
   if(StringCompare(side, "buy") == 0)
   {
       ok = trade.Buy(volume, symbol, 0, sl > 0 ? sl : 0, tp > 0 ? tp : 0);
   }
   else if(StringCompare(side, "sell") == 0)
   {
       ok = trade.Sell(volume, symbol, 0, sl > 0 ? sl : 0, tp > 0 ? tp : 0);
   }

   ulong ticket = 0;
   double executed_price = 0;
   if(ok)
   {
       ticket = trade.ResultOrder();
       executed_price = trade.ResultPrice();
       PrintFormat("Order placed: %s %s vol=%G TP=%G SL=%G ticket=%I64u", symbol, side, volume, tp, sl, ticket);
   }
   else
   {
       PrintFormat("Order failed: %s %s - Error=%d", symbol, side, GetLastError());
   }

   // Report result back to server (best-effort)
   if(StringLen(id) > 0)
   {
       string callbackUrl = CallbackBase + "/" + id + "/executed";
       string payload = StringFormat("{\"ticket\":%I64u,\"status\":\"%s\",\"executed_price\":%G}", ticket, ok?"executed":"failed", executed_price);
       string outHeaders = "Content-Type: application/json\r\n";
       if(StringLen(ApiToken) > 0) outHeaders += "X-EA-Token: " + ApiToken + "\r\n";

       uchar sendBody[];
       int bodyLen = StringToCharArray(payload, sendBody);
       uchar recv[];
       string respHeaders;
       int r = WebRequest("POST", callbackUrl, outHeaders, bodyLen, sendBody, 0, recv, respHeaders);
       if(r == -1)
           Print("Callback WebRequest failed: ", GetLastError());
       else
           Print("Callback sent, response headers: ", respHeaders);
   }

}

// Very small helper to extract value for a key from a flat JSON string
string JsonGetString(const string json, const string key)
{
   string pattern = '"' + key + '"';
   int pos = StringFind(json, pattern);
   if(pos < 0) return("");
   int colon = StringFind(json, ":", pos);
   if(colon < 0) return("");
   int start = colon + 1;
   // skip spaces
   while(start < StringLen(json) && (json[start] == ' ' || json[start] == '\t' || json[start] == '\n' || json[start] == '\r')) start++;
   string val = "";
   // If string value
   if(start < StringLen(json) && json[start] == '"')
   {
       start++;
       int end = StringFind(json, '"', start);
       if(end < 0) return("");
       val = StringSubstr(json, start, end - start);
   }
   else
   {
       // number or literal
       int end = start;
       while(end < StringLen(json) && StringFind(" ,}\]", StringSubstr(json, end, 1)) < 0) end++;
       val = StringSubstr(json, start, end - start);
       val = StringTrim(val);
   }
   return(val);
}

string CharArrayToString(uchar &arr[])
{
   string s = "";
   for(uint i=0;i<ArraySize(arr);i++) s += (char)arr[i];
   return(s);
}

string StringTrim(string s)
{
   int i1=0; while(i1<StringLen(s) && (s[i1]==' '||s[i1]=='\n'||s[i1]=='\r'||s[i1]=='\t')) i1++;
   int i2=StringLen(s)-1; while(i2>=0 && (s[i2]==' '||s[i2]=='\n'||s[i2]=='\r'||s[i2]=='\t')) i2--;
   if(i2<i1) return("");
   return(StringSubstr(s,i1,i2-i1+1));
}

//+------------------------------------------------------------------+
