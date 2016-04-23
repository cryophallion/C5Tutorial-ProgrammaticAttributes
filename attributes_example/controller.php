<?php
namespace Concrete\Package\AttributesExample;

use Package;
use AttributeSet;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use \Concrete\Core\Attribute\Key\CollectionKey as CollectionKey;
use \Concrete\Core\Attribute\Key\FileKey as FileKey;
use \Concrete\Core\Attribute\Key\UserKey as UserKey;
use \Concrete\Core\Attribute\Type as AttributeType;

class Controller extends Package
{
    protected $pkgHandle = 'attributes_example';
    protected $appVersionRequired = '5.7.5.2';
    protected $pkgVersion = '0.0.1';
    protected $previousVersion = '0.0.0';

    public function getPackageDescription()
    {
        return t('Concrete 5.7+ Programmatic Attributes and Attribute Sets example code');
    }

    public function getPackageName()
    {
        return t('Programmatic Attributes Example');
    }

    public function install()
    {
        $pkg = parent::install();
        $this->installOrUpgrade($pkg);
    }

    public function upgrade()
    {
        $pkg = Package::getByHandle($this->pkgHandle);
        $this->previousVersion = $pkg->getPackageVersion();
        parent::upgrade();
        $this->installOrUpgrade($pkg);
    }

    protected function installOrUpgrade($pkg)
    {
        //Create Attribute Sets
        $carAttSet = $this->addAttributeSet('collection', 'car_details', 'Car Details', $pkg);
        
        //Instantiate any KeyCategory classes, for use in the creation function
        $collectionKey = new CollectionKey;
        
        //Create Attributes
        //Attributes are set to a variable in this case with the expectation they will be used elsewhere in the package creation
        //It is always wise to add a prefix to any attributes to distinguish them, as many packages share common attribute handles - in this case, pa, short for Programmatic Attributes
        $carColor = $this->addAttribute('pa_car_color', 'Car Color', 'text', $collectionKey, $carAttSet, $pkg);
        $carMake = $this->addAttribute('pa_car_make', 'Car Make', 'text', $collectionKey, $carAttSet, $pkg);
        $carModel = $this->addAttribute('pa_car_model', 'Car Model', 'text', $collectionKey, $carAttSet, $pkg);
        $carYear = $this->addAttribute('pa_car_year', 'Car Year', 'number', $collectionKey, $carAttSet, $pkg);
        
    }
    

    /**
     * Add Attribute Set
     * @param string $categoryHandle Attribute Key Category Handle
     * @param string $setHandle New Attribute Set Handle
     * @param string $setName New Attribute Set Name
     * @param object $pkg Package Object
     * @return object Attribute Set Object
     */
    protected function addAttributeSet($categoryHandle, $setHandle, $setName, $pkg)
    {
        //Get the Attribute Key Category and ensure that it is set to allow multiple sets
        $pakc = AttributeKeyCategory::getByHandle($categoryHandle);
        $pakc->setAllowAttributeSets(AttributeKeyCategory::ASET_ALLOW_MULTIPLE);

        //get or set Attribute Set
        $att_set = AttributeSet::getByHandle($setHandle);
        if (!is_object($att_set)) {
            $att_set = $pakc->addSet($setHandle, t($setName), $pkg);
        }
        
        return $att_set;
    }
    
        
    /**
     * Add Custom Attribute Key
     * @param string $handle Handle
     * @param string $name Name
     * @param string $type Attribute Type
     * @param object $categoryKeyObject Attribute Key Category Class (ie, CollectionKey, etc class object)
     * @param object $attibuteSetObject Attribute Set Object
     * @param object $pkg Package Object
     * @param boolean $selectAllowOtherValues Sets whether additional values are allowed for select attributes
     * @return object Attribute Object
     */
    protected function addAttribute($handle, $name, $type, $categoryKeyObject, $attibuteSetObject, $pkg, $selectAllowOtherValues = true)
    {
        //get attribut if it's already created
        $attr = $categoryKeyObject::getByHandle($handle);
        if (!is_object($attr)) {
            
            //set default info array
            $info = array(
                'akHandle' => $handle,
                'akName' => $name,
                'akIsSearchable' => true //this is almost always true
            );
            
            //get the attribute type from the handle
            $att_type = AttributeType::getByHandle($type);
            
            //create the attribute then add it to the set
            $attr = $categoryKeyObject::add($att_type, $info, $pkg)->setAttributeSet($attibuteSetObject);
            
            //Set whether select attributes allow additional values
            if ($type == 'select' && $selectAllowOtherValues == true) {
                $attr->setAllowOtherValues();
            }
        }
        
        return $attr;
    }
}