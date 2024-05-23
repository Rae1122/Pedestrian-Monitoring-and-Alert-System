

#define RXp2 16
#define TXp2 17
#include <WiFi.h>
#include <HTTPClient.h>


//network credentials
const char* ssid     = "#############";
const char* password = "";

//Domain name and URL path or IP address with path
const char* serverName = "##############/post-esp-data.php";


// the apiKeyValue value, the PHP file /post-esp-data.php also needs to have the same key 
String apiKeyValue = "##########";


void setup() {
  Serial.begin(115200);
  Serial2.begin(9600, SERIAL_8N1, RXp2, TXp2);
  
  WiFi.begin(ssid, password);
  Serial.println("Connecting");
  while(WiFi.status() != WL_CONNECTED) { 
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.print("Connected to WiFi network with IP Address: ");
  Serial.println(WiFi.localIP());
  
}

void loop() {

  //Check WiFi connection status
  if(WiFi.status()== WL_CONNECTED){
    WiFiClient client;
    HTTPClient http;
    
    // Domain name with URL path or IP address with path
    http.begin(client, serverName);
    
    // Specify content-type header
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    
   float data_to_float;
    
    String data = String(Serial2.readStringUntil('\n')); //reads the string until new line *
    /*multiple readings come within the same time stamp as one second occasionally so values can be put into an array and the largest 
    value can be sent to the string sent via HTTP POST*/ 
    data_to_float = data.toFloat();
   // Serial.print("data_to_float: "); 
    //Serial.println(data_to_float);
    Serial.print("data: "); 
    Serial.println(data); 
    
    if(data_to_float < 1.0) { /* if data is less than the speed limit by a large degree and to filter out any other sources of speed, exit program */
      return;
     }
    
      // Prepare HTTP POST request data
      String httpRequestData ="api_key=" + apiKeyValue + "&speed=" + data + "";

      Serial.print("httpRequestData: ");
      Serial.println(httpRequestData);

      int httpResponseCode = http.POST(httpRequestData); 
 
  
    
    // Send HTTP Post Code        
    if (httpResponseCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
      Serial.println(http.getString());//added
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
   
    http.end();
  }
  else {
    Serial.println("WiFi Disconnected");
  }
}
