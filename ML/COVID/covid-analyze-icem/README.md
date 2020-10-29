# Project to extract and analyze data from Covid-19 in the city of Icem-SP

## Icem-SP

** Icém ** is a Brazilian municipality in the state of São Paulo, on the border with Minas Gerais, with a population of 8,243 inhabitants (IBGE / 2019). It is located close to important urban centers (Barretos, São José do Rio Preto, Catanduva), has large supplies for emitters. It is situated 84 km from Barretos, 55 km from São José do Rio Preto and 112 km from Catanduva. The city is located on the banks of the Rio Grande.
[More information] (https://en.wikipedia.org/wiki/Ic%C3%A9m)

## Dice
The data were obtained through the FanPage of the [Municipality of Icem] (https://www.facebook.com/prefeituradeicem/) and the data include the dates from 03/21/2020 to 06/29/2020, the data were published in daily _posts_ with case data ** investigated **, ** discarded **, ** confirmed **.

## Tools used

### Creeping

The Crawler was screened in python using the [Scrapy] framework (https://scrapy.org/) and using the [Fbcrawl] project (https://github.com/rugantio/fbcrawl) as a base.
It was used to extract the _posts_ from the Facebook FanPage

### Vision AI

To extract data from _posts_ images, it was used in ** Google Vision OCR ** which is a machine learning and AI api from Google Cloud that detects and extracts text from images.

### Selenium

It was used to oppose the images of the Facebook FanPage _posts_. Selenium is a tool used to automate system tests that allows the user to reproduce them quickly in the real environment of the application, due to the direct integration with the browser. By means of an extension, we can create the test scripts in a simple way and use the most diverse programming languages.

### Other Tools
* Pandas
* Python

## Author
[Cesar Augusto] (https://cesarbruschetta.github.io/)