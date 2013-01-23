<?php
/** ClassLoader
 *
 *  Based on SplClassLoader by PHP Standards Working Grounp
 *  <http://groups.google.com/group/php-standards/web/final-proposal>
 *
 *  @author      Sotaro Karasawa <sotaro.k@gmail.com>
 *  @date        2010-12-02 18:59:39
 */

class ClassLoader
{
    private static $_loaders = array();
    private $_file_extension = '.php';
    private $_namespace;
    private $_include_path;
    private $_namespace_separator = '\\';

    /**
     * Creates a new <tt>SplClassLoader</tt> that loads classes of the
     * specified namespace.
     *
     * @param string $ns The namespace to use.
     */
    public function  __construct($ns = null, $includePath = null)
    {
        $this->_namespace = $ns;
        $this->_include_path = realpath($includePath);
    }

    /**
     * Sets the namespace separator used by classes in the namespace of this class loader.
     *
     * @param string $sep The separator to use.
     */
    public function setNamespaceSeparator($sep)
    {
        $this->_namespace_separator = $sep;
    }

    /**
     * Gets the namespace seperator used by classes in the namespace of this class loader.
     *
     * @return void
     */
    public function getNamespaceSeparator()
    {
        return $this->_namespace_separator;
    }

    /**
     * Sets the base include path for all class files in the namespace of this class loader.
     * 
     * @param string $includePath
     */
    public function setIncludePath($includePath)
    {
        $this->_include_path = $includePath;
    }

    /**
     * Gets the base include path for all class files in the namespace of this class loader.
     *
     * @return string $includePath
     */
    public function getIncludePath()
    {
        return $this->_include_path;
    }

    /**
     * Sets the file extension of class files in the namespace of this class loader.
     * 
     * @param string $fileExtension
     */
    public function setFileExtension($fileExtension)
    {
        $this->_file_extension = $fileExtension;
    }

    /**
     * Gets the file extension of class files in the namespace of this class loader.
     *
     * @return string $fileExtension
     */
    public function getFileExtension()
    {
        return $this->_file_extension;
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    /**
     * Uninstalls this class loader from the SPL autoloader stack.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     * @return void
     */
    public function loadClass($className)
    {
        if (null === $this->_namespace
            || $this->_namespace . $this->_namespace_separator
                === substr($className, 0, strlen($this->_namespace . $this->_namespace_separator))
        ) {
            $fileName = '';
            $namespace = '';
            if (false !== ($lastNsPos = strripos($className, $this->_namespace_separator))) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace($this->_namespace_separator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->_file_extension;

            $fn = ($this->_include_path !== null ? $this->_include_path . DIRECTORY_SEPARATOR : '') . $fileName;
            if (file_exists($fn)) {
                require $fn;
            }
        }
    }

    /**
     * Register ClassLoader
     *
     * @static
     * @param string $ns The namespace to use.
     * @return void
     */
    public static function registerNamespace($ns, $path = null)
    {
        $cl = new self($ns, $path);
        $cl->register();

        self::$_loaders[$ns . $path] = $cl;
    }

    /**
     * Unregister ClassLoader
     *
     * @static
     * @param string $ns The namespace to use.
     * @return void
     */
    public static function unregisterNamespace($ns, $path = null)
    {
        if (isset(self::$_loaders[$ns . $path])) {
            self::$_loaders[$ns . $path]->unregister();
        }
    }
}