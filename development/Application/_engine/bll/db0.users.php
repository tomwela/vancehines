<?php

namespace ClickBlocks\DB;

use ClickBlocks\Core,
    ClickBlocks\Cache;


/**
 * @property int $userID
 * @property varchar $firstName
 * @property varchar $lastName
 * @property varchar $email
 * @property varchar $password
 * @property datetime $created
 * @property navigation $aFRatios
 * @property navigation $acceleration
 * @property navigation $customers
 * @property navigation $deceleration
 * @property navigation $eITMSof
 * @property navigation $eITMSon
 * @property navigation $engineDisplacements
 * @property navigation $history
 * @property navigation $iAC
 * @property navigation $idleRPM
 * @property navigation $maps
 * @property navigation $notes
 * @property navigation $sparkFront
 * @property navigation $sparkRear
 * @property navigation $thrtottleProgrsivity1
 * @property navigation $thrtottleProgrsivity2
 * @property navigation $vEfc
 * @property navigation $vErc
 */
class Users extends BLLTable
{
  public  function __construct($pk = null)
  {
    $this->addDAL(new DALUsers(), __CLASS__);
    parent::__construct($pk);
  }

  protected  function _initacceleration()
  {
    $this->navigators['acceleration'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'acceleration');
  }

  protected  function _initeITMSof()
  {
    $this->navigators['eITMSof'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'eITMSof');
  }

  protected  function _initengineDisplacements()
  {
    $this->navigators['engineDisplacements'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'engineDisplacements');
  }

  protected  function _initidleRPM()
  {
    $this->navigators['idleRPM'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'idleRPM');
  }

  protected  function _initmaps()
  {
    $this->navigators['maps'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'maps');
  }

  protected  function _initvErc()
  {
    $this->navigators['vErc'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'vErc');
  }

  protected  function _initaFRatios()
  {
    $this->navigators['aFRatios'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'aFRatios');
  }

  protected  function _initcustomers()
  {
    $this->navigators['customers'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'customers');
  }

  protected  function _initeITMSon()
  {
    $this->navigators['eITMSon'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'eITMSon');
  }

  protected  function _inithistory()
  {
    $this->navigators['history'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'history');
  }

  protected  function _initiAC()
  {
    $this->navigators['iAC'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'iAC');
  }

  protected  function _initvEfc()
  {
    $this->navigators['vEfc'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'vEfc');
  }

  protected  function _initsparkFront()
  {
    $this->navigators['sparkFront'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'sparkFront');
  }

  protected  function _initsparkRear()
  {
    $this->navigators['sparkRear'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'sparkRear');
  }

  protected  function _initnotes()
  {
    $this->navigators['notes'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'notes');
  }

  protected  function _initdeceleration()
  {
    $this->navigators['deceleration'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'deceleration');
  }

  protected  function _initthrtottleProgrsivity1()
  {
    $this->navigators['thrtottleProgrsivity1'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'thrtottleProgrsivity1');
  }

  protected  function _initthrtottleProgrsivity2()
  {
    $this->navigators['thrtottleProgrsivity2'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'thrtottleProgrsivity2');
  }


  protected  function _initeGRFront()
  {
    $this->navigators['eGRFront'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'eGRFront');
  }

  protected  function _initeGRRear()
  {
    $this->navigators['eGRRear'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'eGRRear');
  }

  protected  function _initcrankingFuel()
  {
    $this->navigators['crankingFuel'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'crankingFuel');
  }

  protected  function _initwarmUpEnrichment()
  {
    $this->navigators['warmUpEnrichment'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'warmUpEnrichment');
  }

  protected  function _initiACCrankVSTemp()
  {
    $this->navigators['iACCrankVSTemp'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'iACCrankVSTemp');
  }

  protected  function _initiACCrankStepsToRun()
  {
    $this->navigators['iACCrankStepsToRun'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'iACCrankStepsToRun');
  }


  protected  function _initvEType()
  {
    $this->navigators['vEType'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'vEType');
  }

  protected  function _initthrottleTransGear()
  {
    $this->navigators['throttleTransGear'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'throttleTransGear');
  }

  protected  function _initdBWThrottleLimitGear()
  {
    $this->navigators['dBWThrottleLimitGear'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'dBWThrottleLimitGear');
  }

  protected  function _initactiveExhaustDC1()
  {
    $this->navigators['activeExhaustDC1'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'activeExhaustDC1');
  }

  protected  function _initactiveExhaustDC2()
  {
    $this->navigators['activeExhaustDC2'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'activeExhaustDC2');
  }

  protected  function _initactiveExhaustDC3()
  {
    $this->navigators['activeExhaustDC3'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'activeExhaustDC3');
  }

  protected  function _initactiveExhaustDC4()
  {
    $this->navigators['activeExhaustDC4'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'activeExhaustDC4');
  }

  protected  function _initmAPLoadNorm()
  {
    $this->navigators['mAPLoadNorm'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'mAPLoadNorm');
  }

  protected  function _initinjectorGasConstant()
  {
    $this->navigators['injectorGasConstant'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'injectorGasConstant');
  }

  protected  function _initinjectorSize()
  {
    $this->navigators['injectorSize'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'injectorSize');
  }

  protected  function _initmPGAdj()
  {
    $this->navigators['mPGAdj'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'mPGAdj');
  }

  protected  function _initpEAirFuelRatio()
  {
    $this->navigators['pEAirFuelRatio'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'pEAirFuelRatio');
  }

  protected  function _initcLLambdaRange()
  {
    $this->navigators['cLLambdaRange'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cLLambdaRange');
  }

  protected  function _initgearRatios()
  {
    $this->navigators['gearRatios'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'gearRatios');
  }

  protected  function _initadaptiveKnockRetard()
  {
    $this->navigators['adaptiveKnockRetard'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'adaptiveKnockRetard');
  }

  protected  function _initcTSparkFront()
  {
    $this->navigators['cTSparkFront'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cTSparkFront');
  }

  protected  function _initcTSparkRear()
  {
    $this->navigators['cTSparkRear'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cTSparkRear');
  }

  protected  function _initcTSparkMaxTPS()
  {
    $this->navigators['cTSparkMaxTPS'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cTSparkMaxTPS');
  }

  protected  function _initidleSparkGain()
  {
    $this->navigators['idleSparkGain'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'idleSparkGain');
  }

  protected  function _initidleSparkMax()
  {
    $this->navigators['idleSparkMax'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'idleSparkMax');
  }

  protected  function _initmaxKnockRetard()
  {
    $this->navigators['maxKnockRetard'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'maxKnockRetard');
  }

  protected  function _initpESpark()
  {
    $this->navigators['pESpark'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'pESpark');
  }

  protected  function _initsparkAdjAir()
  {
    $this->navigators['sparkAdjAir'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'sparkAdjAir');
  }

  protected  function _initsparkAdjEng()
  {
    $this->navigators['sparkAdjEng'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'sparkAdjEng');
  }

  protected  function _initsparkAdjHead()
  {
    $this->navigators['sparkAdjHead'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'sparkAdjHead');
  }

  protected  function _initadaptiveContMinTemp()
  {
    $this->navigators['adaptiveContMinTemp'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'adaptiveContMinTemp');
  }

  protected  function _initcLMinTemp()
  {
    $this->navigators['cLMinTemp'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cLMinTemp');
  }

  protected  function _initcLMinTempHyst()
  {
    $this->navigators['cLMinTempHyst'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cLMinTempHyst');
  }

  protected  function _initknockMinTemp()
  {
    $this->navigators['knockMinTemp'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'knockMinTemp');
  }

  protected  function _initknockMinTempHyst()
  {
    $this->navigators['knockMinTempHyst'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'knockMinTempHyst');
  }

  protected  function _initpEDisableRPM()
  {
    $this->navigators['pEDisableRPM'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'pEDisableRPM');
  }

  protected  function _initpEDisableTPS()
  {
    $this->navigators['pEDisableTPS'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'pEDisableTPS');
  }

  protected  function _initpEEnableRPM()
  {
    $this->navigators['pEEnableRPM'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'pEEnableRPM');
  }

  protected  function _initpEEnableTPs()
  {
    $this->navigators['pEEnableTPs'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'pEEnableTPs');
  }

  protected  function _initintakeValveOpen()
  {
    $this->navigators['intakeValveOpen'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'intakeValveOpen');
  }

  protected  function _initintakeValveClose()
  {
    $this->navigators['intakeValveClose'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'intakeValveClose');
  }

  protected  function _initsystemSwitches()
  {
    $this->navigators['systemSwitches'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'systemSwitches');
  }

  protected  function _initspeedoCal()
  {
    $this->navigators['speedoCal'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'speedoCal');
  }
  

  protected  function _initcLBiasFrontCyl()
  {
    $this->navigators['cLBiasFrontCyl'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cLBiasFrontCyl');
  }

  protected  function _initcLBiasRearCyl()
  {
    $this->navigators['cLBiasRearCyl'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cLBiasRearCyl');
  }
  


  protected  function _initidleRPM2()
  {
    $this->navigators['idleRPM2'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'idleRPM2');
  }

  protected  function _initcAMKey()
  {
    $this->navigators['cAMKey'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cAMKey');
  }

  protected  function _initcAMIntakeValveOpenFront()
  {
    $this->navigators['cAMIntakeValveOpenFront'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cAMIntakeValveOpenFront');
  }

  protected  function _initcAMIntakeValveCloseFront()
  {
    $this->navigators['cAMIntakeValveCloseFront'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'cAMIntakeValveCloseFront');
  } 
  

  protected  function _initrevLimitArray()
  {
    $this->navigators['revLimitArray'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'revLimitArray');
  }

    protected  function _initrevLimitOffset()
  {
    $this->navigators['revLimitOffset'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'revLimitOffset');
  }

  //////////////////////////////////////////////////////////////
   protected  function _initspeedLimDIs()
  {
    $this->navigators['speedLimDIs'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'speedLimDIs');
  }

   protected  function _initspeedLImEn()
  {
    $this->navigators['speedLImEn'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'speedLImEn');
  }

   protected  function _initspeedLimFront()
  {
    $this->navigators['speedLimFront'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'speedLimFront');
  }

   protected  function _initspeedLimRear()
  {
    $this->navigators['speedLimRear'] = new \ClickBlocks\DB\NavigationProperty($this, __CLASS__, 'speedLimRear');
  }



}  //Users
?>