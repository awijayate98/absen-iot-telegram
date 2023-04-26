
void recvMsg(uint8_t *data, size_t len){
  WebSerial.println("Received Data...");
  String d = "";
  for(int i=0; i < len; i++){
    d += char(data[i]);
  }
  WebSerial.println(d);
  if (d == "RESET"){
    ESP.restart();
  }
  else if (d=="WIFI_RESET"){
    wifiManager.resetSettings();
    delay(500);
    SPIFFS.format();
     delay(500);
     ESP.restart();
  }
}


void initSPIFFS(){
  Serial.println("mounting FS...");

  if (SPIFFS.begin()) {
    Serial.println("mounted file system");
    if (SPIFFS.exists("/config_host.json")) {
      //file exists, reading and loading
      Serial.println("reading config file");
      File configFile = SPIFFS.open("/config_host.json", "r");
      if (configFile) {
        Serial.println("opened config file");
        size_t size = configFile.size();
        // Allocate a buffer to store contents of the file.
        std::unique_ptr<char[]> buf(new char[size]);
        configFile.readBytes(buf.get(), size);
        DynamicJsonDocument json(1024);
         auto deserializeError = deserializeJson(json, buf.get());
        serializeJson(json, Serial);
        if (! deserializeError) {
          Serial.println("\nparsed json");
          strcpy(host_server, json["host_server"]);
          strcpy(pilih_modec, json["pilih_modec"]);
        } else {
          Serial.println("failed to load json config");
        }
      }
    }

    
  } else {
    Serial.println("failed to mount FS");
  }
  //end read
}

void save_pengaturan() {
    Serial.println("saving config");
    DynamicJsonDocument json(1024);
    json["host_server"] = host_server;
    json["pilih_modec"] = pilih_modec;
    File configFile = SPIFFS.open("/config_host.json", "w");
    if (!configFile) 
    {
      Serial.println("file creation failed");
    } else {
      Serial.println("File Created!");
      serializeJson(json,configFile);
      configFile.close();
    //end save
      shouldSaveConfig = false;
    }
    configFile.close();
}

void saveConfigCallback () {
  Serial.println(F("Should save config"));
  shouldSaveConfig = true;
}


void initialized() {
  initSPIFFS();
  WiFiManagerParameter custom_host_server("HOST", "HOST SERVER", host_server, 50);
  WiFiManagerParameter custom_mode("MODE", "MODE RUNNING", pilih_modec, 10);
  wifiManager.setSaveConfigCallback(saveConfigCallback);
  wifiManager.addParameter(&custom_host_server);
  wifiManager.addParameter(&custom_mode);
  if (!wifiManager.autoConnect("Absen", "12345678")) {
    lcd.clear();
    lcd.print("MASUK KE SET WIFI");
    lcd.setCursor(0,1);
    lcd.print("192.168.4.1 ");
    delay(1000);
    Serial.println("failed to connect and hit timeout");
    delay(3000);
    ESP.restart();
    delay(3000);
  }
  inisial_ota();
  debug_program();
  strcpy(host_server, custom_host_server.getValue());
  host_servers = String(host_server);
  strcpy(pilih_modec, custom_mode.getValue());
  pilih_mode = atoi(pilih_modec);
  if (shouldSaveConfig) 
  {
    save_pengaturan();
  }
  lcd.setCursor(0,0);
  lcd.print("TERKONEKSI");
  lcd.setCursor(0,1);
  lcd.print("KE WIFI ");

}

void inisial_ota()
{
  server.on("/", HTTP_GET, [](AsyncWebServerRequest *request) {
    request->send(200, "text/plain", "Hi! I am ESP32.");
  });
  WebSerial.begin(&server);
  WebSerial.msgCallback(recvMsg);
  AsyncElegantOTA.begin(&server);
  server.begin();
}

void debug_program(){
 if(digitalRead(tombol_mode)==ditekan)
  {
    mode_debug = true;
    delay(200);
  }
    while(mode_debug == true)
    {
//      ArduinoOTA.handle();
      lcd.setCursor(0,0);
      lcd.print("MODE PROGRAM");
    Serial.println("MODE PROGRAM");
  }
}
