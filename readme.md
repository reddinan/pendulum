# Pendulum
### A framework agnostic library for importing data into your PHP applications 
Pendulum allows you to import data into your application from files, APIs, data stores, or any other source you want to pull information from. 
Simply specify a data source and provide a function specifying how to import your data into your specific aplication and Pendulum will handle the rest!

Pendulum uses [PHP Iterators](http://php.net/manual/en/class.iterator.php) as data sources meaning it will work out of the box with any [PHP Standard Library Iterator](http://php.net/manual/en/spl.iterators.php) or any data source that implements the Iterator Interface. In the majority of use cases, a data source will already exist meaning all the code you need to provide is a small function explaining how to import this data into your specific application.

Pendulum is framework agnostic, meaning it will work with any PHP 7 codebase, but if you are using this in Laravel or Symfony based applications, A pre made console application can be used to import data from the comfort of your command line or even a scheduled cron job. 

### Installation 

    composer require bytepath/pendulum
    
The library should be automatically installed via the Laravel package auto-discovery feature. 

### Specifying Your Data Source

Pendulum uses [PHP Iterators](http://php.net/manual/en/class.iterator.php) as data sources meaning it can import data using any [PHP Standard Library Iterator](http://php.net/manual/en/spl.iterators.php) or any custom object that implements the Iterator Interface. 

Making an Iterator sound like too much work? Pendulum also supports [PHP Generators](http://php.net/manual/en/language.generators.overview.php). You can also simply pass pendulum a php array, though in that case this library might be overkill for your needs.

### Specifying Your Import Class

Pendulum accepts any object or class that implements the Pendulum ImporterInterface class. The Interface, listed in its entirety below, has one method, processItem, that specifies how to import data into your application.

Success, Failure, and Duplicates are all application specific information, so the processItem method expects the return value to be one of the constants listed in the interface below. This return value lets Pendulum know whether your application was able to import this item. 

    interface ImporterContract
    {
        // Successfully imported item
        const IMPORT_SUCCESS = 1;
    
        
        // Failed to import item
        const IMPORT_FAILED = -1;
    
        
        // Item was already imported
        const ALREADY_IMPORTED = 0;
        
        /**
         * Import a single piece of data into your application
         * Returns one of the following constants 
         * ImporterContract::IMPORT_SUCCESS -- We imported the data sucessfully
         * ImporterContract::IMPORT_FAILED -- We failed to import this item
         * ImporterContract::ALREADY_IMPORTED -- This is a duplicate item
         * 
         * @param PendulumContract $item The item you want to import into the system
         * @return integer returns one of the constants listed in the ImporterContract Interface
         */
        public function processItem(&$item);
    }
    

### Specifying Output Writer
Pendulum can output information about the current import to the user by providing a class that implements the Pendulum OutputContract Interface. Simply implement this interface in an object of your choice and pass this object as a dependency when instantiating the base Pendulum class. See "Instantiating And Using Pendulum In Your Applications" for more information. If no class that implements the Pendulum OutputContract is provided, NullOutputWriter will be used by default and will essentially throw away any output intended for the user.

### Instantiating And Using Pendulum In Your Applications

When instantiating Pendulum, we need to specify 
+ A class implementing the [PHP Iterator](http://php.net/manual/en/class.iterator.php) Interface that will be used as a data source to pull data we wish to import into our application
+ A data import class that implements the Pendulum ImporterContract interface. will save the data we want to import into our application
+ (Optional) an output writer writer class implementing the Pendulum OutputWriter interface that will display data to the end user.
 
These interfaces can either be implemented by a single "mega class", or split apart into multiple classes as necessary. The Pendulum constructor accepts any number of arguments so you can split apart Interfaces as needed as long as you are supplying objects that implement the interfaces mentioned above. 


