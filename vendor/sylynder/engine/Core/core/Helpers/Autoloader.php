<?php

namespace Base\Helpers;

/**
 * PSR-4 Autoloader for Webby App Directory
 * 
 * Initially by Nick Tsai
 * @author  Nick Tsai <myintaer@gmail.com>
 * @since 1.0.0
 * @see  https://github.com/yidas/codeigniter-psr4-autoload
 */
class Autoloader
{
    /**
     * @var string Nampsapce prefix refered to App Root
     */
    const string DEFAULT_PREFIX = "App";
    
    /**
     * Register Autoloader
     * 
     * @param string $prefix PSR-4 namespace prefix
     */
    // public static function register($prefix = null)
    // {
    //     $prefix = ($prefix) ? (string) $prefix : self::DEFAULT_PREFIX;
        
    //     spl_autoload_register(function ($classname) use ($prefix) {
    //         // Prefix check
    //         if (strpos(strtolower($classname), "{$prefix}\\")===0) {
    //             // Locate class relative path
    //             $classname = str_replace("{$prefix}\\", "", $classname);
    //             $filepath = APPROOT.  str_replace('\\', DIRECTORY_SEPARATOR, ltrim($classname, '\\')) . '.php';
                
    //             if (file_exists($filepath)) {
                    
    //                 require $filepath;
    //             }
    //         }
    //     });
    // }

    public static function autoload($classe): void 
    { 
        $namespace= static::getPrincipalNamespace($classe);
        
        $namespace = APPROOT. $namespace . '.php';
        $namespace = str_replace('\\', DS, $namespace);
    
        if (! file_exists($namespace) || is_dir($namespace)) { 
            throw new \Exception("******* This Class don't exists!! --->>>> ".$namespace. "*********\n");
        }
    
        require $namespace;
    }
    
    static function getPrincipalNamespace($namespace): ?string 
    { 
        $configs = (array) static::getConfigIfExists();

        if(!isset($configs)) { 
            return  $namespace;    
        }

        $principalNamespace = explode('\\', $namespace)[0];
       
        foreach ($configs as $key => $value) {
            $keyReplaced = str_replace('\\', '', $key);
    
            if ($principalNamespace == $keyReplaced) {
                return str_replace($key, $value, $namespace);
            }
        }
    
        return $namespace;
    }
    
    static function getConfigIfExists(): ?object 
    { 
        $maping = static::getWebbyJsonToObject();
        return $maping->autoload ?? null;
    } 
    
    static function getWebbyJsonToObject(): ?object 
    { 
        $file = ROOTPATH . 'webby.json';
        return (file_exists($file)) ? json_decode(file_get_contents($file)) : null;
    }
    
}
