#!/usr/bin/python
import RPi.GPIO as GPIO
import time
from RPLCD import *
from time import sleep
from RPLCD.i2c import CharLCD
import mysql.connector

# Initialize the LCD instance
lcd = CharLCD('PCF8574', 0x27)

# Initialize GPIO
GPIO.setmode(GPIO.BCM)
GPIO.setwarnings(False)
BUZZER = 24
GPIO.setup(BUZZER, GPIO.OUT)

# Establish connection to the MySQL database
conn = mysql.connector.connect(user='admin', password='your_password', host='localhost', database='esp_data')
conn.autocommit = True
cursor = conn.cursor()

def process_pass():
    print("Processing PASS result...")

    def sound_buzzer_and_print():
        # Make a beep sound
        GPIO.output(BUZZER, GPIO.HIGH)
        # Set the cursor position and write warn of oncoming traffic
        lcd.clear()
        lcd.cursor_pos = (0, 0)
        lcd.write_string('Speeding Motorist')
        lcd.cursor_pos = (1, 4)
        lcd.write_string('Oncoming')
        lcd.cursor_pos = (2, 0)
        lcd.write_string('Clear Crosswalk Now')
        print("Beep")
        time.sleep(2)  # Delay in seconds
        GPIO.output(BUZZER, GPIO.LOW)
        print("No Beep")
        time.sleep(1)

    sound_buzzer_and_print()

    # Clean up GPIO
    # GPIO.cleanup()


def process_fail():
    #for trailing text 
    print("Processing FAIL result...")
    framebuffer = [
        '',
        '',
    ]

    def write_to_lcd(lcd, framebuffer, num_cols):
        """Write the framebuffer out to the specified LCD."""
        lcd.home()
        for row in framebuffer:
            lcd.write_string(row.ljust(num_cols)[:num_cols])
            lcd.write_string('\r\n')

    def long_text(text):
        lcd.clear()
        if len(text) < 20:
            lcd.write_string(text)
        else:
            for i in range(0, len(text), 20):
                framebuffer[1] = text[i:i + 20]
                write_to_lcd(lcd, framebuffer, 20)
                sleep(0.4)

    # Set the cursor position and write "default text"
    lcd.cursor_pos = (1, 0)
    # while True:  # repeat as default screen
    long_text('Safe journeys. Always stay alert from PMAS')
    sleep(1)  # delay for 1 sec

while True:
    """query from the Sensor Data table, the last entered values over 2km/h within 10 seconds to return PASS"""
    query = "SELECT speed, IF(speed > 2.0, 'PASS', 'FAIL') FROM SensorData WHERE reading_time >= NOW() - INTERVAL 10 SECOND ORDER BY id DESC LIMIT 1"
    cursor.execute(query)
    results= cursor.fetchone()


    if results is not None:
        print(results[0])
        if results[1] == 'PASS':
            process_pass()
        elif results[1] == 'FAIL':
            process_fail()
    else: 
        process_fail()

    # time.sleep(5)

conn.close()
