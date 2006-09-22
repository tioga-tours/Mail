<?php
/**
 * File containing the ezcMailFile class
 *
 * @package Mail
 * @version //autogen//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Mail part for binary data from the file system.
 *
 * @package Mail
 * @version //autogen//
 */
class ezcMailFile extends ezcMailFilePart
{
    /**
     * Constructs a new attachment with $fileName.
     *
     * @param string $fileName
     * @return void
     */
    public function __construct( $fileName )
    {
        parent::__construct( $fileName );

        if ( ezcBaseFeatures::hasExtensionSupport( 'fileinfo' ) )
        {
            // get mime and content type
            $fileInfo = finfo_open( FILEINFO_MIME );
            $mimeParts = finfo_file($fileInfo, $fileName);
            if ( $mimeParts !== false && strpos( $mimeParts, '/' ) !== false )
            {
                list( $this->contentType, $this->mimeType ) = explode( '/', $mimeParts );
            }
            else
            {
                // default to mimetype application/octet-stream
                $this->contentType = self::CONTENT_TYPE_APPLICATION;
                $this->mimeType = "octet-stream";
            }
            finfo_close( $fileInfo );
        }
        else
        {
            // default to mimetype application/octet-stream
            $this->contentType = self::CONTENT_TYPE_APPLICATION;
            $this->mimeType = "octet-stream";
        }
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @throws ezcBaseFileNotFoundException when setting the property with an invalid filename.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'fileName':
                if ( is_readable( $value ) )
                {
                    parent::__set( $name, $value );
                }
                else
                {
                    throw new ezcBaseFileNotFoundException( $value );
                }
                break;
            default:
                return parent::__set( $name, $value );
                break;
        }
    }

    /**
     * Returns the value of property $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            default:
                return parent::__get( $name );
                break;
        }
    }

    /**
     * Returns the contents of the file with the correct encoding.
     *
     * @return string
     */
    public function generateBody()
    {
        return chunk_split( base64_encode( file_get_contents( $this->fileName ) ), 76, ezcMailTools::lineBreak() );
    }
}
?>