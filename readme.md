# FlashBang
### A simple library to assist with flashing alert messages to users of your Laravel applications. 
Display context specific messages to your users that will be displayed once then never shown again.

In addition to creating flash messages, this library allows you to write to the Laravel log file to allow you to keep track of what has been shown to the user
#### Installation 

    composer require bytepath/flashbang
    
The library should be automatically installed via the Laravel package auto-discovery feature. 

#### Adding a Flash Message To The Session

To add a flash message to the session use the FlashBang facade included in this package.
    
    use Bytepath\FlashBang\Facades\FlashBang;

You can now send a message to the user of your application using one of the methods listed below
   
    /**
     * The requested action was successfully completed
     * @param $message The message to display to the user
     * @param $logMessage an optional message to add to the laravel log file
     */
        FlashBang::success($message, $logMessage = null);
     
    /**
     * The requested action failed to complete
     * @param $message The message to display to the user
     * @param $logMessage an optional message to add to the laravel log file
     */
        FlashBang::failure($message); 
        
    /**
     * Display an informational message to the user
     * @param $message The message to display to the user
     * @param $logMessage an optional message to add to the laravel log file
     */
        FlashBang::info($message);    
        
    /**
     * Display a warning message to the user
     * @param $message The message to display to the user
     * @param $logMessage an optional message to add to the laravel log file
     */
        FlashBang::warning($message); 
        
#### Adding Flash Message To Views

To add a FlashBang message to your view, simply add the following snippet 

    @include("flashbang::messages")
to whatever view you wish to display these messages on. Messages can be displayed multiple times on the same page if for
whatever reason you wanted to do that.

By default this sub view will also show Form validation errors (and any other session errors).
If you don't want this add $hideSessionErrors = true to the include directive
    
    @include("flashbang::messages", ["hideSessionErrors" => true])
 