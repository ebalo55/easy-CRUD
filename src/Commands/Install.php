<?php

namespace Ebalo\EasyCRUD\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'easy-crud:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the default controller to include the easy-crud commands';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
	    $this->info("Installing easy-crud to the main controller class");
	    $path = app_path("Http/Controllers/Controller.php");
	    $content = file_get_contents($path);

	    $use = "use Ebalo\EasyCRUD\EasyCRUD;";
	    $trait = "use EasyCRUD;";

	    // The controller is missing the usage statement
	    if(!Str::contains($content, $use)) {
		    // Retrieve the first character of the use statement
		    $position = strpos($content, "use");
		    // Split the string and insert in between the required usage statement
		    $content = Str::substr($content, 0, $position) . $use . PHP_EOL . Str::substr($content, $position);
	    }

	    // The controller is missing the use statement inside the body of the controller
	    if(!preg_match("/(use|,)\s*EasyCRUD\s*(;|,)/", $content)) {
		    // Retrieve the first character of the last usage statement
		    $position = strrpos($content, "use");
		    $content = Str::substr($content, 0, $position) . $trait . PHP_EOL . "\t" . Str::substr($content, $position);
	    }

	    file_put_contents($path, $content);
	    $this->info("Installation completed successfully");
        return 0;
    }
}
