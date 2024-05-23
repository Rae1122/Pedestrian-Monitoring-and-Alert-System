#include <LiquidCrystal_I2C.h>
#include <Wire.h>
#include <FreqMeasure.h>

LiquidCrystal_I2C lcd(0x27,20,4);  // set the LCD address to 0x27 for a 20 chars and 4 line display
// GND - GND
// VCC - 5V
// SDA - ANALOG Pin 4
// SCL - ANALOG pin 5
 
 
double sum = 0;
int count = 0;
 
float f;   // Frequency
float v;   // speed
 
 
// ========================
// ======== SETUP =========
// ========================
 
void setup()
   {
  
   Serial.begin(9600); // to serially send to esp32
    
    lcd.init();                      // initialize the lcd
 
    // Print a message to the LCD.
    lcd.backlight();
    
    lcd.setCursor(0,0);
    lcd.print("The PMAS");
    lcd.setCursor(0,1);
    lcd.print("by");
    lcd.setCursor(0,2);
    lcd.print("Murray Corp");
    lcd.setCursor(0,3);
    lcd.print("Safety for all");
    
    delay(10000);
    
    lcd.setCursor(0,0);
    lcd.print("                       ");
    lcd.setCursor(0,1);
    lcd.print("                       ");
    lcd.setCursor(0,2);
    lcd.print("                       ");
    lcd.setCursor(0,3);
    lcd.print("                       ");  
    
    lcd.setCursor(0,0);
    lcd.print("Your speed:");
    
    FreqMeasure.begin();
   }
 
 
// ================================
// ======== Main Loop =========
// ================================
 
void loop()
   {
   if (FreqMeasure.available())
       {
        // average the readings together
        sum = sum + FreqMeasure.read();

        
        count = count + 1;
    
        if (count >= 30)
           {
            f = FreqMeasure.countToFrequency(sum / count);
            
            
            v = (f / 19.49);       // conversion from frequency to kilometers per hour

                       
            lcd.setCursor(12,0);
            lcd.print(v);
            lcd.print("km/h");

            if (v >= 1.0)
            {
              
              lcd.setCursor(0,1);
              lcd.print("                    ");
              lcd.setCursor(0,2);
              lcd.print("                    ");
              lcd.setCursor(0,3);
              lcd.print("                    "); 

                 
              lcd.setCursor(0,1);
              lcd.print("Slow Down");
              lcd.setCursor(0,2);
              lcd.print("Pedestrian Crosswalk");
              lcd.setCursor(0,3);
              lcd.print("Ahead");
             }
            else{
              
              lcd.setCursor(0,1);
              lcd.print("                    ");
              lcd.setCursor(0,2);
              lcd.print("                    ");
              lcd.setCursor(0,3);
              lcd.print("                    "); 
                   
              lcd.setCursor(0,1);
              lcd.print("Safe Travels");
              lcd.setCursor(0,2);
              lcd.print("From Murray Corp");
              }
           
           Serial.println(String(v)); 
           delay(50);
                        
            sum = 0;
            count = 0;
           
           }
       }
   }
