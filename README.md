# class.einzahlungsschein.phpEine Klasse um Einzahlungsscheine mit ESR-Nummer als PDF zu erstellen.A class to create Swiss payment slips with ESR number in pdf format.See http://sprain.ch/blog/downloads/class-esr-besr-einzahlungsschein-php/## Installation- Get FPDF <http://fpdf.org/>- Get OCRB font <http://sprain.ch/blog/wp-content/uploads/2010/04/OCRB_FPDFConverted.zip>- Move OCRB font files into the font folder of the FPDF class.- See example_orange.php or example_red.php and get started.## Thanks to- <http://www.smoke8.net/> for public designs of Einzahlungsscheinen- <http://www.developers-guide.net/forums/5431,modulo10-rekursiv> for Modulo10 function- <http://ansuz.sooke.bc.ca/software/ocrb.php> for OCRB font- <http://blog.fruit-lab.de/fpdf-font-converter/> for FPDF font converter- <http://www.fpdf.de/> for the pdf class## History- 2012/11/13 - PSR-0 compatibility, added composer- 2012/10/31 - minor bugfixes, better readme- 2012/04/16 - added functionality for red Einzahlungsscheine, added possibility of $ezs_payerFullAddress, changed class name to `Einzahlungsschein`, several minor improvements- 2011/12/22 - got rid of GNU license. Do whatever you want with it.- 2011/05/31 - improved behaviour of $this->ezs_bankingCustomerIdentification, minor bugfixes- 2011/02/14 - added project to Github, again- 2010/05/06 - added project to Github- 2010/05/06 - corrected position on bottom line after feedback from bank- 2010/04/24 - when it all started## AuthorsOriginal library written by Manuel Reinhard, <manu@sprain.ch>. Converted to a PSR-0 library with the ability to use it with composer by Peter siska, <pesche@gridonic.ch>.## LicenseMIT, see LICENSE.## Help- <https://github.com/sprain/class.Einzahlungsschein.php>- <http://sprain.ch/blog/downloads/class-esr-besr-einzahlungsschein-php/>