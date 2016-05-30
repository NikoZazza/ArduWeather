#include <SPI.h>
#include <EthernetV2_0.h>
//Variabili per la scheda ethernet ws5200
#define SS    10 
#define nRST  8  
#define nPWDN 9  
#define nINT 3
//Variabili per definire i PIN
#define PIN_TMP 0
#define PIN_LED 5

byte mac[] = {0xDE, 0xAD, 0xBE, 0xEF, 0xEE, 0xEE};
IPAddress ip(10,0 ,1, 99); //ip della nostra scheda di rete
IPAddress server(10,0,1,36); //ip del server in cui verranno spediti i valori
EthernetClient client;

int myKey = 1; //la chiave con cui identifichiamo il sensore
float temperatura = 0.0; //variabile della temperatura corrente
int ciclo = 0; //per prelevare un secondo valore dal sensore, dobbiamo aspettare almeno 1sec
float minRange = -127.0; //valore minimo in cui l'allarme si deve attivare
float maxRange = 127.0; //valore massimo in cui l'allarme si deve attivare
bool next = false; //valore che serve a memorizzare prima la variabile del valore minimo, poi memorizza il valore massimo

void setup() {
    Serial.begin(9600); //apriamo la seriale per il debug
    //impostazioni della schede di rete
    pinMode(SS, OUTPUT);  
    pinMode(nRST, OUTPUT);
    pinMode(nPWDN, OUTPUT);
    pinMode(nINT, INPUT); 
    digitalWrite(nPWDN, LOW);   
    digitalWrite(nRST, LOW);  
    delay(10);
    digitalWrite(nRST,HIGH); 
    delay(200);       
    //fine impostazioni della scheda di rete
    pinMode(PIN_LED, OUTPUT);
    digitalWrite(PIN_LED, LOW);
    
    Ethernet.begin(mac, ip);
    Serial.print("L'ip della scheda e': ");
    Serial.println(Ethernet.localIP());
}

void loop() {
  int tmp = 200; //il tempo del delay finale
  if(ciclo >= 5){
    ciclo = -1;
    if(client.connect(server, 80)){
        Serial.print("Connesso al server di destinazione e mando la temperatura ");
        Serial.print(temperatura);
        Serial.println(" C");
        String var = "GET /query.php?id=";
        var +=(int) myKey;
        var += "&temperatura=";
        var += (float)temperatura;
        var +=" HTTP/1.1";
        Serial.println(var);
        client.println(var);
        client.println("Host: localhost");
        client.println("User-Agent: arduino-ethernet");
        client.println("Connection: close");
        client.println();        
        
        int timeout = millis() + 5000;
        while (client.available() == 0) {
          if (timeout - millis() < 0) {
            Serial.println("Problemi con il client!");
            client.stop();
            return;
          }
        }
        String line;
        String lastLine;
        String value;
        while(client.available()){
            value = lastLine;
            lastLine = line;
            line = client.readStringUntil('\n');
        }
        char * test;
        char test1[50];
        value.toCharArray(test1, 50);
        test = strtok(test1, "%");
        while(test != NULL){
            if(next){
                maxRange = atoi(test);
                break;
            }else{
                minRange = atoi(test);
            }
            next = !next;
            test = strtok(NULL, "%");
        } 
        client.stop();  
    }else{
        Serial.println("Connessione fallita al server di destinazione!");
    }    
  }        
  temperatura = 0.0;
  for (byte i = 0; i < 5; i++) { //Esegue l'istruzione successiva 5 volte
    temperatura += (analogRead(analogRead(PIN_TMP)) / 9.31); //Calcola la temperatura e la somma alla variabile 'temp'
  }
  temperatura /= 5; //Calcola la media dei  valori di temperatura                   //to degrees ((voltage - 500mV) times 100)
  if(temperatura >= maxRange || temperatura <= minRange){
    digitalWrite(PIN_LED, HIGH);  
    delay(150);
    digitalWrite(PIN_LED, LOW);  
    tmp = 50;
  }
  ciclo++;
  delay(tmp);     
}

