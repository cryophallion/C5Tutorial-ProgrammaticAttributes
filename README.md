# Concrete5 Tutorial - Programmatic Attributes
Concrete5.7+ example code for creating attributes and attribute sets programmatically.

## Installation
Copy the `attributes_example` folder into the packages folder in the root of your Concrete5 site. Then go to the Dashboard->Extend Concrete5 and then click install next to the Attributes Example package.

After installation, go to Dashboard->Pages & Themes->Attributes. You will find the Car Details Attribute Set and the Attribute Keys installed there.

## Notes
This is a slightly more abstracted function than is shown in the [Adding Attribute Sets and Keys Progammatically](http://documentation.concrete5.org/developers/packages/adding-attribute-sets-and-keys-programmatically) tutorial.
In this version, the addition of sets and keys are moved into their own function that can be easily re-used and which allows for installing all types of Attribute Sets and Attribute Keys.

# UPDATE
Due to the C5 documentation page for some reason deleting a tutorial, I am now adding them into the readme files:

## Background
One of the great features of concrete5, and the deepest keys to extending it, is using attributes. These attributes can be just about anything that is connected to a page, a user, or a file. Default ones for page include the page title, description, etc. This can also include things like a client or task in the portfolio in the demo site. If each page is a type of car, attributes could include color choices, description, model year, almost anything, and they can be attached to the page to be called up within the page. Users can have a favorite flower attribute for a garden club. A file can have an extra type signalling if it is a drawing, specification, or addenda for a construction company. There are endless uses for them.

Since this is such a vital thing, and many packages will need to use them but don't have access to the front end for every client, knowing how to install them in your controller is vital (adding them to page types and things of that nature is another topic). Additionally, we can add attribute sets to organize our attributes.

## Example Specification
For this code example, we will add a page attribute for Color, which will be a simple text box for entering a color (you could also use a select box for this, but simplicity, we will stay with a text box). Our package is named autos, and the set will be named Car Details. This code would usually be in a function that is run every time the package is installed or upgraded, such as an `installOrUpgrade` function. This way everything is installed, even new features, for end users. Therefore, the $pkg variable will have to be passed to the function, from whichever higher level function is calling it.

## Attribute Categories
As mentioned earlier, attributes can be used for a page, a user, or a key. Actually, these are all attribute keys. More keys can be added for other objects, but usually, these are the keys that cover the vast majority of scenarios. Before we can add attributes, we have to know which type of key they will be. They are CollectionKey (for a page), UserKey (for a user), and FileKey (for files). 

To get the types, we will need to pull in the handler to get the correct category object at the head of the controller:

    use \Concrete\Core\Attribute\Key\Category as AttributeCategory;
    
and then later to check to see if the key is in that category, we will need to call that category's key

    use \Concrete\Core\Attribute\Key\CollectionKey as CollectionKey;
    
## Attribute Types
Next, we have to have access to an attribute type. Again, you can create your own, but the included options of address, boolean (yes/no), date/time, image/file, number, rating, select, text, and textarea serve most situations. We will later have to pull in one of these, so let's get the handler to get the different attribute type objects

    use \Concrete\Core\Attribute\Type\ as AttributeType;

## Adding Attribute Sets
Finally, we will need to have the core item for Attribute Sets. This one is at least shorthand, and would go in the header as:

    use AttributeSet;
    
So, now that we have our core items pulled in, let's create the code to add a set in our function:

First, we get the correct category:

    $collectionCat = AttributeCategory::getByHandle('collection');
    
Then, we have to make sure that we allow a new set to be made. So we set the parameter to a defined setting that allows more sets:

    $collectionCat->setAllowAttributeSets(AttributeKeyCategory::ASET_ALLOW_MULTIPLE);

Now, this code would likely be run during an install or upgrade procedure, so there is a chance our new set already exists, and we don't want to duplicate it. So, we will first see if there is already a set with that name:

    $set = AttributeSet::getByHandle('car_details');
    
Then we test that, and if it is not already created, we create it:

    if (!is_object($set)) {
        $set = $collectionCat->addSet('car_details',t('Car Details'), $pkg); 
        //again, the $pkg variable is assumed to be sent to this function from the install or upgrade functions
    }
    
And that's it! We can now use that set to add attributes.

## Adding Attributes
Finally, down to the adding of our attributes. Well, almost. Before we can add them, we have to get the attribute type information. As this is a text box, we get it by getting the type by handle:

    $text = AttributeType::getByHandle('text');
    
And NOW we can add the type. First though, we have to see if it exists (again). Actually, many times this next part of it's code will be in it's own function, and each attribute that needs to be installed just loops through this to install it. For this example, we will just keep going with the objects we have.

Again, we first check if it exists, and if not, then we can install it. We do that by passing an array of info to the add function (only some of the options are listed here, more are available), and then daisy chain on adding it to our new set:
    
    $color = CollectionKey::getByHandle('color');
    if (!is_object($color)) {
        $data = array(
            'akHandle' => 'color',
            'akName' => 't(Color)',
            'akIsSearchable' => true
        );
        CollectionKey::add($text,$data,$pkg)->setAttributeSet($set);
    }
    
And that's it. Now there is a new page attribute of color in the Car Details set, which you can pull in using a page attributes block in a PageType for example.
