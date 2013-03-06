<?php
/**
 * Swiss Inpayment Slip as PDF
 *
 * PHP version >= 5.3.0
 *
 * @licence MIT
 * @copyright 2012-2013 Some nice Swiss guys
 * @author Manuel Reinhard <manu@sprain.ch>
 * @author Peter Siska <pesche@gridonic.ch>
 * @author Marc Würth ravage@bluewin.ch
 * @link https://github.com/sprain/class.Einzahlungsschein.php
 * @version: 0.4.0
 */

namespace Gridonic\ESR;

use InvalidArgumentException;

// TODO include CHF boxed slip image (609, ESR+)
// TODO include EUR framed slip image (701) --> back side!
// TODO include EUR boxed slip image (701) --> back side!
// TODO implement notForInpaying (XXXX.XX)
// TODO implement cash on delivery (Nachnahme)
// TODO include cash on delivery (Nachnahme) slip image
// TODO create constants for the attribute keys
// TODO create constants for left, right and center text alignment (L, R, C)
// TODO create central cell placement and formatting code (lines as array, attributes)...
// TODO code cleanup
// TODO create tests, what's possible (getter and setter, setter without getters testable?
// TODO docblocks
// TODO class docblock
// TODO test docs generated

/**
 * A general purpose class for swiss inpayment slips. Data is organized by its sister class SwissInpaymentSLipData.
 */
class SwissInpaymentSlip
{
	/**
	 * The inpayment slip value object, which contains the inpayment slip data
	 *
	 * @var SwissInpaymentSlipData The inpayment slip value object
	 */
	protected $inpaymentSlipData = null;

	/**
	 * Starting X position of the slip
	 *
	 * @var int Starting X position of the slip in mm
	 */
	protected $slipPosX = 0;

	/**
	 * Starting Y position of the slip
	 *
	 * @var int Starting Y position of the slip in mm
	 */
	protected $slipPosY = 191;

	/**
	 * @var int
	 */
	protected $slipHeight = 106; // default height of an orange slip

	/**
	 * @var int
	 */
	protected $slipWidth = 210; // default width of an orange slip

	/**
	 * Background of the slip
	 *
	 * Can be either transparent, a color or image
	 *
	 * @var null
	 */
	protected $slipBackground = null;

	/**
	 * @var string
	 */
	protected $defaultFontFamily = 'Helvetica';

	/**
	 * @var string
	 */
	protected $defaultFontSize = '10';

	/**
	 * @var string
	 */
	protected $defaultFontColor = '#000';

	/**
	 * @var int
	 */
	protected $defaultLineHeight = 4;

	/**
	 * @var string
	 */
	protected $defaultTextAlign = 'L';

	/**
	 * Determines if the bank details should be displayed
	 *
	 * @var bool True if yes, false if no
	 */
	protected $displayBank = true;

	/**
	 * Determines if the recipient details should be displayed
	 *
	 * @var bool True if yes, false if no
	 */
	protected $displayRecipient = true;

	/**
	 * Determines if the account should be displayed
	 *
	 * @var bool True if yes, false if no
	 */
	protected $displayAccount = true;

	/**
	 * Determines if the amount should be displayed
	 *
	 * @var bool True if yes, false if no
	 */
	protected $displayAmount = true;

	/**
	 * Determines if the reference number should be displayed
	 *
	 * @var bool True if yes, false if no
	 */
	protected $displayReferenceNr = true;

	/**
	 * Determines if the payer details should be displayed
	 *
	 * @var bool True if yes, false if no
	 */
	protected $displayPayer = true;

	/**
	 * Determines if the IBAN should be displayed
	 *
	 * @var bool True if yes, false if no
	 */
	protected $displayIban = false;

	/**
	 * Determines if the payment reason should be displayed
	 *
	 * @var bool True if yes, false if no
	 */
	protected $displayPaymentReason = false;

	/**
	 * Determines if the code line at the bottom should be displayed
	 *
	 * @var bool True if yes, false if no
	 */
	protected $displayCodeLine = true;

	/**
	 * @var array
	 */
	protected $bankLeftAttr = array();

	/**
	 * @var array
	 */
	protected $bankRightAttr = array();

	/**
	 * @var array
	 */
	protected $recipientLeftAttr = array();

	/**
	 * @var array
	 */
	protected $recipientRightAttr = array();

	/**
	 * @var array
	 */
	protected $accountLeftAttr = array();

	/**
	 * @var array
	 */
	protected $accountRightAttr = array();

	/**
	 * @var array
	 */
	protected $amountFrancsLeftAttr = array();

	/**
	 * @var array
	 */
	protected $amountFrancsRightAttr = array();

	/**
	 * @var array
	 */
	protected $amountCentsLeftAttr = array();

	/**
	 * @var array
	 */
	protected $amountCentsRightAttr = array();

	/**
	 * @var array
	 */
	protected $referenceNumberLeftAttr = array();

	/**
	 * @var array
	 */
	protected $referenceNumberRightAttr = array();

	/**
	 * @var array
	 */
	protected $payerLeftAttr = array();

	/**
	 * @var array
	 */
	protected $payerRightAttr = array();

	/**
	 * @var array
	 */
	protected $codeLineAttr = array();

	/**
	 * @param SwissInpaymentSlipData $inpaymentSlipData
	 * @param integer $slipPosX
	 * @param integer $slipPosY
	 *
	 * @throws \InvalidArgumentException
	 * @todo Implement width and height as optional parameters
	 */
	public function __construct($inpaymentSlipData, $slipPosX = null, $slipPosY = null)
	{
		if (!is_object($inpaymentSlipData)) {
			throw new InvalidArgumentException('InpaymentSlipData parameter is not an object!');
		}
		if (!$inpaymentSlipData instanceof SwissInpaymentSlipData) {
			throw new InvalidArgumentException('InpaymentSlipData parameter is not an instance of SwissInpaymentSlipData!');
		}
		$this->inpaymentSlipData = $inpaymentSlipData;

		if (!is_null($slipPosX)) {
			$this->setSlipPosX($slipPosX);
		}
		if (!is_null($slipPosY)) {
			$this->setSlipPosY($slipPosY);
		}
		$this->setDefaults();
	}

	/**
	 * Sets the default attributes of the elements according to the type
	 *
	 * @todo Eliminate the direct system dependency, make the background image a mandatory constructor parameter
	 */
	protected function setDefaults()
	{
		if ($this->inpaymentSlipData->getType() == SwissInpaymentSlipData::ORANGE) {
			$this->setBankLeftAttr(3, 8, 50, 4);
			$this->setBankRightAttr(66, 8, 50, 4);
			$this->setRecipientLeftAttr(3, 23, 50, 4);
			$this->setRecipientRightAttr(66, 23, 50, 4);
			$this->setAccountLeftAttr(27, 43, 30, 4);
			$this->setAccountRightAttr(90, 43, 30, 4);
			$this->setAmountFrancsLeftAttr(5, 50.5, 35, 4);
			$this->setAmountFrancsRightAttr(66, 50.5, 35, 4);
			$this->setAmountCentsLeftAttr(50, 50.5, 6, 4);
			$this->setAmountCentsRightAttr(111, 50.5, 6, 4);
			$this->setReferenceNumberLeftAttr(3, 60, 50, 4, null, null, 8);
			$this->setReferenceNumberRightAttr(125, 33.5, 80, 4);
			$this->setPayerLeftAttr(3, 65, 50, 4);
			$this->setPayerRightAttr(125, 48, 50, 4);
			$this->setCodeLineAttr(64, 85, 140, 4, null, 'OCRB10');

			// TODO Eliminate system dependency
			$this->setSlipBackground(__DIR__.'/Resources/img/ezs_orange.gif');

		} elseif ($this->inpaymentSlipData->getType() == SwissInpaymentSlipData::RED) {
			$this->setBankLeftAttr(3, 8, 50, 4);
			$this->setBankRightAttr(66, 8, 50, 4);
			$this->setRecipientLeftAttr(3, 23, 50, 4);
			$this->setRecipientRightAttr(66, 23, 50, 4);
			$this->setAccountLeftAttr(27, 43, 30, 4);
			$this->setAccountRightAttr(90, 43, 30, 4);
			$this->setAmountFrancsLeftAttr(5, 50.5, 35, 4);
			$this->setAmountFrancsRightAttr(66, 50.5, 35, 4);
			$this->setAmountCentsLeftAttr(50, 50.5, 6, 4);
			$this->setAmountCentsRightAttr(111, 50.5, 6, 4);
			$this->setReferenceNumberLeftAttr(3, 60, 50, 4, null, null, 8);
			$this->setReferenceNumberRightAttr(125, 33.5, 80, 4);
			$this->setPayerLeftAttr(3, 65, 50, 4);
			$this->setPayerRightAttr(125, 48, 50, 4);
			$this->setCodeLineAttr(64, 85, 140, 4, null, 'OCRB10');

			// TODO Eliminate system dependency
			$this->setSlipBackground(__DIR__.'/Resources/img/ezs_red.gif');
		}
	}

	/**
	 * @return SwissInpaymentSlipData
	 */
	public function getInpaymentSlipData() {
		return $this->inpaymentSlipData;
	}

	/**
	 * Set slip X & Y position
	 *
	 * @param $slipPosX
	 * @param $slipPosY
	 * @return bool True if successful, else false
	 */
	public function setSlipPosition($slipPosX, $slipPosY)
	{
		if ($this->setSlipPosX($slipPosX) &&
			$this->setSlipPosY($slipPosY)) {
			return true;
		}
		return false;
	}

	/**
	 * @param $slipPosX
	 * @return bool
	 */
	protected function setSlipPosX($slipPosX)
	{
		if (is_int($slipPosX) || is_float($slipPosX)) {
			$this->slipPosX = $slipPosX;
			return true;
		}
		return false;
	}

	/**
	 * @param $slipPosY
	 * @return bool
	 */
	protected function setSlipPosY($slipPosY)
	{
		if (is_int($slipPosY) || is_float($slipPosY)) {
			$this->slipPosY = $slipPosY;
			return true;
		}
		return false;
	}

	/**
	 * Set slip height & width
	 *
	 * @param $slipWidth
	 * @param $slipHeight
	 * @return bool True if successful, else false
	 */
	public function setSlipSize($slipWidth, $slipHeight)
	{
		if ($this->setSlipHeight($slipHeight) &&
			$this->setSlipWidth($slipWidth)) {
			return true;
		}
		return false;
	}

	/**
	 * @param $slipWidth
	 * @return bool
	 */
	protected function setSlipWidth($slipWidth)
	{
		if (is_int($slipWidth) || is_float($slipWidth)) {
			$this->slipWidth = $slipWidth;
			return true;
		}
		return false;
	}

	/**
	 * @param $slipHeight
	 * @return bool
	 */
	protected function setSlipHeight($slipHeight)
	{
		if (is_int($slipHeight) || is_float($slipHeight)) {
			$this->slipHeight = $slipHeight;
			return true;
		}
		return false;
	}

	/**
	 * @param string $slipBackground
	 * @return bool Always true
	 *
	 * @todo Implement sanity checks on parameter (filename or color)
	 */
	public function setSlipBackground($slipBackground)
	{
		$this->slipBackground = $slipBackground;

		return true;
	}

	/**
	 * @param $attributes
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	protected function setAttributes(&$attributes, $posX = null, $posY = null, $width = null, $height = null, $background = null,
								   $fontFamily = null, $fontSize = null, $fontColor = null,
								   $lineHeight = null, $textAlign = null) {
		if ($posX) {
			$attributes['PosX'] = $posX;
		} elseif (!isset($attributes['PosX'])) {
			$attributes['PosX'] = 0;
		}
		if ($posY) {
			$attributes['PosY'] = $posY;
		} elseif (!isset($attributes['PosY'])) {
			$attributes['PosY'] = 0;
		}
		if ($width) {
			$attributes['Width'] = $width;
		} elseif (!isset($attributes['Width'])) {
			$attributes['Width'] = 0;
		}
		if ($height) {
			$attributes['Height'] = $height;
		} elseif (!isset($attributes['Height'])) {
			$attributes['Height'] = 0;
		}
		if ($background) {
			$attributes['Background'] = $background;
		} elseif (!isset($attributes['Background'])) {
			$attributes['Background'] = 'transparent';
		}
		if ($fontFamily) {
			$attributes['FontFamily'] = $fontFamily;
		} elseif (!isset($attributes['FontFamily'])) {
			$attributes['FontFamily'] = $this->defaultFontFamily;
		}
		if ($fontSize) {
			$attributes['FontSize'] = $fontSize;
		} elseif (!isset($attributes['FontSize'])) {
			$attributes['FontSize'] = $this->defaultFontSize;
		}
		if ($fontColor) {
			$attributes['FontColor'] = $fontColor;
		} elseif (!isset($attributes['FontColor'])) {
			$attributes['FontColor'] = $this->defaultFontColor;
		}
		if ($lineHeight) {
			$attributes['LineHeight'] = $lineHeight;
		} elseif (!isset($attributes['LineHeight'])) {
			$attributes['LineHeight'] = $this->defaultLineHeight;
		}
		if ($textAlign) {
			$attributes['TextAlign'] = $textAlign;
		} elseif (!isset($attributes['TextAlign'])) {
			$attributes['TextAlign'] = $this->defaultTextAlign;
		}
		return true;

	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setBankLeftAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->bankLeftAttr, $posX, $posY, $width, $height, $background, $fontFamily,
									$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setBankRightAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->bankRightAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setRecipientLeftAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->recipientLeftAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setRecipientRightAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->recipientRightAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setAccountLeftAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->accountLeftAttr, $posX, $posY, $width, $height, $background,	$fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setAccountRightAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->accountRightAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setAmountFrancsLeftAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		if (!$textAlign) {
			$textAlign = 'R';
		}

		return $this->setAttributes($this->amountFrancsLeftAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setAmountFrancsRightAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		if (!$textAlign) {
			$textAlign = 'R';
		}

		return $this->setAttributes($this->amountFrancsRightAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setAmountCentsLeftAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->amountCentsLeftAttr, $posX, $posY, $width, $height, $background,	$fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setAmountCentsRightAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->amountCentsRightAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setReferenceNumberLeftAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->referenceNumberLeftAttr, $posX, $posY, $width, $height, $background,	$fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setReferenceNumberRightAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		if (!$textAlign) {
			$textAlign = 'R';
		}

		return $this->setAttributes($this->referenceNumberRightAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setPayerLeftAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->payerLeftAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setPayerRightAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		return $this->setAttributes($this->payerRightAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @param null $posX
	 * @param null $posY
	 * @param null $width
	 * @param null $height
	 * @param null $background
	 * @param null $fontFamily
	 * @param null $fontSize
	 * @param null $fontColor
	 * @param null $lineHeight
	 * @param null $textAlign
	 * @return bool
	 */
	public function setCodeLineAttr($posX = null, $posY = null, $width = null, $height = null,
		$background = null,	$fontFamily = null, $fontSize = null, $fontColor = null,
		$lineHeight = null, $textAlign = null) {
		if (!$textAlign) {
			$textAlign = 'R';
		}

		return $this->setAttributes($this->codeLineAttr, $posX, $posY, $width, $height, $background, $fontFamily,
			$fontSize, $fontColor, $lineHeight, $textAlign);
	}

	/**
	 * @return array
	 */
	public function getAccountLeftAttr()
	{
		return $this->accountLeftAttr;
	}

	/**
	 * @return array
	 */
	public function getAccountRightAttr()
	{
		return $this->accountRightAttr;
	}

	/**
	 * @return array
	 */
	public function getAmountCentsRightAttr()
	{
		return $this->amountCentsRightAttr;
	}

	/**
	 * @return array
	 */
	public function getAmountCentsLeftAttr()
	{
		return $this->amountCentsLeftAttr;
	}

	/**
	 * @return array
	 */
	public function getAmountFrancsLeftAttr()
	{
		return $this->amountFrancsLeftAttr;
	}

	/**
	 * @return array
	 */
	public function getAmountFrancsRightAttr()
	{
		return $this->amountFrancsRightAttr;
	}

	/**
	 * @return array
	 */
	public function getBankLeftAttr()
	{
		return $this->bankLeftAttr;
	}

	/**
	 * @return array
	 */
	public function getBankRightAttr()
	{
		return $this->bankRightAttr;
	}

	/**
	 * @return array
	 */
	public function getCodeLineAttr()
	{
		return $this->codeLineAttr;
	}

	/**
	 * @return array
	 */
	public function getRecipientRightAttr()
	{
		return $this->recipientRightAttr;
	}

	/**
	 * @return array
	 */
	public function getRecipientLeftAttr()
	{
		return $this->recipientLeftAttr;
	}

	/**
	 * @return array
	 */
	public function getPayerRightAttr()
	{
		return $this->payerRightAttr;
	}

	/**
	 * @return array
	 */
	public function getPayerLeftAttr()
	{
		return $this->payerLeftAttr;
	}

	/**
	 * @return array
	 */
	public function getReferenceNumberLeftAttr()
	{
		return $this->referenceNumberLeftAttr;
	}

	/**
	 * @return array
	 */
	public function getReferenceNumberRightAttr()
	{
		return $this->referenceNumberRightAttr;
	}

	/**
	 * @return null
	 */
	public function getSlipBackground()
	{
		return $this->slipBackground;
	}

	/**
	 * @return int
	 */
	public function getSlipPosX()
	{
		return $this->slipPosX;
	}

	/**
	 * @return int
	 */
	public function getSlipPosY()
	{
		return $this->slipPosY;
	}

	/**
	 * @return int
	 */
	public function getSlipWidth()
	{
		return $this->slipWidth;
	}

	/**
	 * @return int
	 */
	public function getSlipHeight()
	{
		return $this->slipHeight;
	}

	/**
	 * Set whether or not to display the account
	 *
	 * @param bool $displayAccount True if yes, false if no
	 * @return bool True if successful, else false
	 */
	public function setDisplayAccount($displayAccount = true)
	{
		if (is_bool($displayAccount)) {
			$this->displayAccount = $displayAccount;
			return true;
		}
		return false;
	}

	/**
	 * Get whether or not to display the account
	 *
	 * @return bool True if yes, false if no
	 */
	public function getDisplayAccount()
	{
		return $this->displayAccount;
	}

	/**
	 * Set whether or not to display the amount
	 *
	 * @param bool $displayAmount True if yes, false if no
	 * @return bool True if successful, else false
	 */
	public function setDisplayAmount($displayAmount = true)
	{
		if (is_bool($displayAmount)) {
			$this->displayAmount = $displayAmount;
			return true;
		}
		return false;
	}

	/**
	 * Get whether or not to display the amount
	 *
	 * @return bool True if yes, false if no
	 */
	public function getDisplayAmount()
	{
		return $this->displayAmount;
	}

	/**
	 * Set whether or not to display the bank
	 *
	 * @param bool $displayBank True if yes, false if no
	 * @return bool True if successful, else false
	 */
	public function setDisplayBank($displayBank = true)
	{
		if (is_bool($displayBank)) {
			$this->displayBank = $displayBank;
			return true;
		}
		return false;
	}

	/**
	 * Get whether or not to display the bank
	 *
	 * @return bool True if yes, false if no
	 */
	public function getDisplayBank()
	{
		return $this->displayBank;
	}

	/**
	 * Set whether or not to display the payer
	 *
	 * @param bool $displayPayer True if yes, false if no
	 * @return bool True if successful, else false
	 */
	public function setDisplayPayer($displayPayer = true)
	{
		if (is_bool($displayPayer)) {
			$this->displayPayer = $displayPayer;
			return true;
		}
		return false;
	}

	/**
	 * Get whether or not to display the payer
	 *
	 * @return bool True if yes, false if no
	 */
	public function getDisplayPayer()
	{
		return $this->displayPayer;
	}

	/**
	 * Set whether or not to display the recipient
	 *
	 * @param bool $displayRecipient True if yes, false if no
	 * @return bool True if successful, else false
	 */
	public function setDisplayRecipient($displayRecipient = true)
	{
		if (is_bool($displayRecipient)) {
			$this->displayRecipient = $displayRecipient;
			return true;
		}
		return false;
	}

	/**
	 * Get whether or not to display the recipient
	 *
	 * @return bool True if yes, false if no
	 */
	public function getDisplayRecipient()
	{
		return $this->displayRecipient;
	}

	/**
	 * Set whether or not to display the reference number
	 *
	 * @param bool $displayReferenceNr True if yes, false if no
	 * @return bool True if successful, else false
	 */
	public function setDisplayReferenceNr($displayReferenceNr = true)
	{
		if (is_bool($displayReferenceNr)) {
			$this->displayReferenceNr = $displayReferenceNr;
			return true;
		}
		return false;
	}

	/**
	 * Get whether or not to display the reference number
	 *
	 * @return bool True if yes, false if no
	 */
	public function getDisplayReferenceNr()
	{
		return $this->displayReferenceNr;
	}

	/**
	 * Set whether or not to display the IBAN
	 *
	 * @param bool $displayIban True if yes, false if no
	 * @return bool True if successful, else false
	 */
	public function setDisplayIban($displayIban = true)
	{
		if (is_bool($displayIban)) {
			$this->displayIban = $displayIban;
			return true;
		}
		return false;
	}

	/**
	 * Get whether or not to display the IBAN
	 *
	 * @return bool True if yes, false if no
	 */
	public function getDisplayIban()
	{
		return $this->displayIban;
	}

	/**
	 * Set whether or not to display the payment reason lines
	 *
	 * @param bool $displayPaymentReason True if yes, false if no
	 * @return bool True if successful, else false
	 */
	public function setDisplayPaymentReason($displayPaymentReason = true)
	{
		if (is_bool($displayPaymentReason)) {
			$this->displayPaymentReason = $displayPaymentReason;
			return true;
		}
		return false;
	}

	/**
	 * Get whether or not to display the payment reason lines
	 *
	 * @return bool True if yes, false if no
	 */
	public function getDisplayPaymentReason()
	{
		return $this->displayPaymentReason;
	}

	/**
	 * Set whether or not to display the code line at the bottom
	 *
	 * @param bool $displayCodeLine True if yes, false if no
	 * @return bool True if successful, else false
	 */
	public function setDisplayCodeLine($displayCodeLine = true)
	{
		if (is_bool($displayCodeLine)) {
			$this->displayCodeLine = $displayCodeLine;
			return true;
		}
		return false;
	}

	/**
	 * Get whether or not to display the code line at the bottom
	 *
	 * @return bool True if yes, false if no
	 */
	public function getDisplayCodeLine()
	{
		return $this->displayCodeLine;
	}

	/**
	 * @param bool $formatted
	 * @param bool $fillZeroes
	 * @return array
	 */
	public function getAllElements($formatted = true, $fillZeroes = true) {
		$inpaymentSlipData = $this->inpaymentSlipData;

		$elements = array();
		// Place left bank lines
		if ($this->getDisplayBank()) {
			$lines = array($inpaymentSlipData->getBankName(),
				$inpaymentSlipData->getBankCity());
			$elements['bankLeft'] = array('lines' => $lines,
				'attributes' => $this->getBankLeftAttr()
			);
		}

		// Place right bank lines
		if ($this->getDisplayBank()) {
			// reuse lines from above
			$elements['bankRight'] = array('lines' => $lines,
				'attributes' => $this->getBankRightAttr()
			);
		}

		// Place left recipient lines
		if ($this->getDisplayRecipient()) {
			$lines = array($inpaymentSlipData->getRecipientLine1(),
				$inpaymentSlipData->getRecipientLine2(), $inpaymentSlipData->getRecipientLine3(),
				$inpaymentSlipData->getRecipientLine4());
			$elements['recipientLeft'] = array('lines' => $lines,
				'attributes' => $this->getRecipientLeftAttr()
			);
		}

		// Place right recipient lines
		if ($this->getDisplayRecipient()) {
			// reuse lines from above
			$elements['recipientRight'] = array('lines' => $lines,
				'attributes' => $this->getRecipientRightAttr()
			);
		}

		// Place left account number
		if ($this->getDisplayAccount()) {
			$lines = array($inpaymentSlipData->getAccountNumber());
			$elements['accountLeft'] = array('lines' => $lines,
				'attributes' => $this->getAccountLeftAttr()
			);
		}

		// Place right account number
		if ($this->getDisplayAccount()) {
			// reuse lines from above
			$elements['accountRight'] = array('lines' => $lines,
				'attributes' => $this->getAccountRightAttr()
			);
		}

		// Place left amount in francs
		if ($this->getDisplayAmount()) {
			$lines = array($this->inpaymentSlipData->getAmountFrancs());
			$elements['amountFrancsLeft'] = array('lines' => $lines,
				'attributes' => $this->getAmountFrancsLeftAttr()
			);
		}

		// Place right amount in francs
		if ($this->getDisplayAmount()) {
			// reuse lines from above
			$elements['amountFrancsRight'] = array('lines' => $lines,
				'attributes' => $this->getAmountFrancsRightAttr()
			);
		}

		// Place left amount in cents
		if ($this->getDisplayAmount()) {
			$lines = array($this->inpaymentSlipData->getAmountCents());
			$elements['amountCentsLeft'] = array('lines' => $lines,
				'attributes' => $this->getAmountCentsLeftAttr()
			);
		}

		// Place right amount in cents
		if ($this->getDisplayAmount()) {
			// reuse lines from above
			$elements['amountCentsRight'] = array('lines' => $lines,
				'attributes' => $this->getAmountCentsRightAttr()
			);
		}

		// Place left reference number
		if ($this->getDisplayReferenceNr()) {
			$lines = array($this->inpaymentSlipData->getCompleteReferenceNumber($formatted, $fillZeroes));
			$elements['referenceNumberLeft'] = array('lines' => $lines,
				'attributes' => $this->getReferenceNumberLeftAttr()
			);
		}

		// Place right reference number
		if ($this->getDisplayReferenceNr()) {
			// reuse lines from above
			$elements['referenceNumberRight'] = array('lines' => $lines,
				'attributes' => $this->getReferenceNumberRightAttr()
			);
		}

		// Place left payer lines
		if ($this->getDisplayPayer()) {
			$lines = array($inpaymentSlipData->getPayerLine1(),
				$inpaymentSlipData->getPayerLine2(), $inpaymentSlipData->getPayerLine3(),
				$inpaymentSlipData->getPayerLine4());
			$elements['payerLeft'] = array('lines' => $lines,
				'attributes' => $this->getPayerLeftAttr()
			);
		}

		// Place right payer lines
		if ($this->getDisplayPayer()) {
			// reuse lines from above
			$elements['payerRight'] = array('lines' => $lines,
				'attributes' => $this->getPayerRightAttr()
			);
		}

		// Place code line
		if ($this->getDisplayCodeLine()) {
			$lines = array($this->inpaymentSlipData->getCodeLine($fillZeroes));
			$elements['codeLine'] = array('lines' => $lines,
				'attributes' => $this->getCodeLineAttr()
			);
		}

		return $elements;
	}
}