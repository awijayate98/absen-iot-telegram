
/* 
 *  db password = jjRQNV4Iz@i&_h)1
 *  
 *  
 *  */
#include <Wire.h> 
#include <LiquidCrystal_I2C.h>
#include <SPI.h>
#include <RFID.h>
#include <Ultrasonic.h>
#include <Adafruit_MLX90614.h>  //SENSOR SUHU 
#include <WiFiManager.h> // https://github.com/tzapu/WiFiManager
#include <WiFi.h>
#include <NTPClient.h>
#include <WiFiUdp.h>
#include <HTTPClient.h>
#include "SPIFFS.h"
#include <ArduinoJson.h>
#include <WiFi.h>
#include <AsyncTCP.h>
#include <ESPAsyncWebServer.h>
#include <AsyncElegantOTA.h>
#include <WebSerial.h>


#define ditekan 0
//mode
#define mode_daftar 3
#define mode_matkul1 1
#define mode_matkul2 2
#define otomatis 1
#define manual 0
#define kirim_mode 1
#define kirim_data 2
//pin alat
#define SS_PIN 4
#define RST_PIN 5
#define echo 15
#define trigger 2
#define p_buzzer 13
#define p_led1 12 //KUNING
#define p_led2 14 //HIJAU
#define tombol_mode 27

//waktu 
#define jam_matkulp1  2 //jam matkul 1 pagi
#define jam_matkulp2  10 //jam matkul 2 pagi
#define jam_selesaip  11 //jam selesai kuliah  pagi

#define jam_matkulm1  13
#define jam_matkulm2  3
#define jam_selesaim  21

#define menit_matkul1  30
#define menit_matkul2  15

char pilih_modec[10] = "0";
char host_server[50] = "http://192.168.137.1/absen";
String host_servers = "";
String url_kirim = "/kirimkartu.php?nokartu=";
String url_mode = "/ubahmode.php?mode=";
String url_getdata= "/ambilset.php";
const long utcOffsetInSeconds = 25200;
char daysOfTheWeek[7][12] = {"Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"};
int mode_alat;
int distance;
float temp = 0.0;
String idkartu , teks;
String formattedDate;
String dayStamp;
String timeStamp;
String statusabsen = "";
int menit,jam,detik;
boolean counter_absen = false;
byte count;
byte flag;
int pilih_mode;
bool shouldSaveConfig = false;
bool mode_debug = false;
WiFiManager wifiManager;
RFID rfid(SS_PIN, RST_PIN); 
LiquidCrystal_I2C lcd(0x27, 16, 2);
Ultrasonic ultrasonic(trigger,echo);
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "id.pool.ntp.org", utcOffsetInSeconds);
Adafruit_MLX90614 mlx = Adafruit_MLX90614();
AsyncWebServer server(80);
void load()
{
 if(WiFi.status()== WL_CONNECTED)
{
  const int httpPort = 80;
  HTTPClient http;
  String Link;
//  HTTPClient http;
  Link = host_servers+"/ambilset.php";
  http.begin(Link.c_str());
  int httpCode = http.GET();
  mode_alat = http.getString().toInt();
  Serial.println(httpCode);
  lcd.clear();
  lcd.setCursor(0,0);
  lcd.print(httpCode);
  delay(1000);
  http.end();
}else {
      Serial.println("WiFi Disconnected");
      lcd.clear();
      lcd.setCursor(0,0);
      lcd.print("WIFI DISKONEK");
      
    }
}

void setup()
{ 
  Serial.begin(115200);
  lcd.begin(); 
  pinMode(p_led1, OUTPUT);
  pinMode(p_led2, OUTPUT);
  pinMode(p_buzzer, OUTPUT);
  pinMode(tombol_mode, INPUT_PULLUP);
  WiFi.begin(WiFi.SSID().c_str(), WiFi.psk().c_str());
  initialized();
   if (!mlx.begin()) {
    Serial.println("Error connecting to MLX sensor. Check wiring.");
    while (1);
  };
  
  load();
  timeClient.begin();
  SPI.begin(); 
  rfid.init();
  Serial.println("Ready");

}

void loop()
{

    loop_mode(pilih_mode);
   Serial.println(counter_absen);
}


void loop_mode(int pilih)
{
  if(pilih == otomatis)
  {
   waktu_absen_otomatis(); 
  }else{
    gantimode();
  }
  if(counter_absen == true)
  {
   // 
    if (flag == 0)
    {
      lcd.clear();
      tampillcd(0);
      bacarfid();
    }else{
      lcd.clear();
      Serial.print("Suhu");
      lcd.setCursor(0,0);
      lcd.print("CEK TEMPERATURE");
      
      bacasensor();
      if(distance <= 5)
      {
        for(int c= 0;  c < 5; c++)
        {
          bacasensor();
          lcd.setCursor(0,1);
          lcd.print("SUHU ANDA : ");
          lcd.print(temp);
          Serial.println(c);
          digitalWrite(p_led1, HIGH);
          digitalWrite(p_led2, LOW);
          Serial.println(c);
          delay(500);
          digitalWrite(p_led2, HIGH);
          digitalWrite(p_led1, LOW);
          delay(500);      
        }
        if (distance <= 5)
        {
          if(temp > 37)
          {
           statusabsen = "PERIKSA_KE_DOKTER";
           digitalWrite(p_led2, LOW);
           digitalWrite(p_led1, HIGH);
           teks+="&temperature=";
          teks+=temp;
          teks+="&statusabsen=";
          teks+=statusabsen;
          }else{
            statusabsen = "Suhu_Normal";
            digitalWrite(p_led2, HIGH);
            digitalWrite(p_led1, LOW);
             teks+="&temperature=";
          teks+=temp;
          teks+="&statusabsen=";
          teks+=statusabsen;
          }
            lcd.clear();
            lcd.setCursor(0,0);
            lcd.print("SUHU ANDA : ");
            lcd.print(temp);
            lcd.setCursor(0,1);
            lcd.print(statusabsen);
            kirimdata(kirim_data,teks);
            delay(1000);
            flag = 0;
            teks = "";
        }else{
          flag = 0;
        }
      }
    }
  }else{
    Serial.println("MODE DAFTAR");
    delay(1000);
     bacarfid();
  }

}

void gantimode()
{
  if(digitalRead(tombol_mode)==ditekan)   //ditekan
  {
    digitalWrite(p_buzzer,HIGH);
    mode_alat++;
    lcd.clear();
    delay(500);
    digitalWrite(p_buzzer,LOW);
    if(mode_alat > 3)
    {
      mode_alat = 1;
    }
     kirimdata(kirim_mode,String(mode_alat));
  }
  if(mode_alat < mode_daftar){
   
     counter_absen = true;
     digitalWrite(p_led1, LOW);
    digitalWrite(p_led2, LOW);
  }else{
     counter_absen = false;
     tampillcd(1);
     digitalWrite(p_led1, HIGH);
    digitalWrite(p_led2, HIGH);
  }
  
}

void waktu_absen_manual()
{
  bacarfid();
}

void waktu_absen_otomatis()
{
  waktu();
  //MATKUL 1
  if((jam >= jam_matkulp1 && jam <= jam_matkulp2) || (jam >= jam_matkulm1 && jam <= jam_matkulm2) )
  {
   if(menit >= menit_matkul1)
   {
      counter_absen = true;
      mode_alat = mode_matkul1;
      kirimdata(kirim_mode,String(mode_alat));
     if(menit >= menit_matkul1 && menit <= menit_matkul1 + 15)
     {
      statusabsen = "DISIPLIN";
     }else{
      statusabsen = "TERLAMBAT";
     }
       
   }
   //MATKUL 2
  }else if((jam >= jam_matkulp2 && jam <= jam_selesaip) || (jam >= jam_matkulm2 && jam <= jam_selesaim) )
  {
  if(menit >= menit_matkul2)
   {
    counter_absen = true;
    mode_alat = mode_matkul2;
    kirimdata(kirim_mode,String(mode_alat));
     if(menit >= menit_matkul2 && menit <= menit_matkul2 + 15)
     {
      statusabsen = "DISIPLIN";
     }else{
      statusabsen = "TERLAMBAT";
     }
   }
  }else{
   counter_absen = false;
  } 
}


void bacasensor()
{
  distance = ultrasonic.read();
  temp = mlx.readObjectTempC();
}

void waktu()
{
  timeClient.update();
  formattedDate = timeClient.getFormattedDate();
  int splitT = formattedDate.indexOf("T");
  formattedDate = formattedDate.substring(0, splitT);
  dayStamp = daysOfTheWeek[timeClient.getDay()];
  jam = timeClient.getHours();
  menit =  timeClient.getMinutes();
  detik = timeClient.getSeconds();
  Serial.print(jam);
  Serial.print(" : ");
  Serial.print(menit);
  Serial.print(":");
  Serial.println(detik);
}

void tampillcd(byte a)
{
  if(a == 0)
  {
    lcd.setCursor(0,0);
    lcd.print("SCAN KARTU RFID");
    lcd.setCursor(0,1);
    lcd.print("MATKUL ");
    lcd.print(mode_alat);
  }else if( a == 1)
  {
    lcd.setCursor(0,0);
    lcd.print("SCAN KARTU RFID");
    lcd.setCursor(0,1);
    lcd.print("MODE DAFTAR ");
  }else if(a == 2)
  {
    lcd.setCursor(0,0);
    lcd.print("ID KARTU ANDA");
    lcd.setCursor(0,1);
    lcd.print(teks);
  }
}


void bacarfid()
{ 
 if(rfid.isCard())
 {
  if(rfid.readCardSerial()) 
  { 
   idkartu="";
   lcd.clear();
   for (int i=0; i<= 4; i++) 
   { 
    idkartu += String(rfid.serNum[i],DEC);
   }
   beep(50);
   teks = idkartu;
   tampillcd(2);
   if(mode_alat == mode_daftar)
   {
    flag = 0;
    kirimdata(kirim_data,idkartu);
   }else{
    flag = 1;
   }
   
   Serial.println(idkartu);
   delay(200);
 }
 }
 rfid.halt();
 delay(1000);
}

void beep(int buzer)
{
  digitalWrite(p_buzzer,HIGH);
  delay(buzer);
  digitalWrite(p_buzzer,LOW);
  delay(buzer);
  
}
void kirimdata(int i,String pesan)
{
   if(WiFi.status()== WL_CONNECTED)
   {
      const int httpPort = 80;
     
      String url = "";
      HTTPClient http;
      if(i == 1)
      {
        url = host_servers +url_mode+pesan;
      }else if( i == 2)
      {
        url = String(host_server)+url_kirim+pesan;
      }
      Serial.println(url);
      http.begin(url.c_str());
      int httpCode = http.GET();
      String payload = http.getString();
      Serial.println(payload);
      Serial.println(httpCode);
      if(httpCode == 200)
      {
         lcd.clear();
        if(mode_alat < 3)
        {
        lcd.print("ABSEN BERHASIL"); 
        delay(500);
        }else{
          lcd.print("INPUT BERHASIL"); 
        delay(500);
        }
        beep(50);
      }else{
        lcd.print("INPUT GAGAL");
        beep(200);
        delay(500);
      }
    
      http.end();
   }else {
      Serial.println("WiFi Disconnected");
      lcd.clear();
      lcd.setCursor(0,0);
      lcd.print("WIFI DISKONEK");
      
    }
}
