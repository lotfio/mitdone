<?php namespace MITDone\Http;

/**
 * Simple PHP upload class
 *
 * @author Aivis Silins
 */
class FileUpload
{
    /**
     * @var string|array image name
     */
    public $name;
    /**
     * @var string|array image tmp location
     */
    private $tmp_name;
    /**
     * @var string|array image type
     */
    private $type;
    /**
     * @var string|array image size
     */
    private $size;
    /**
     * @var array|string image errors
     */
    private $errors;
    /**
     * @var string|array image extension
     */
    public $ext;
    /**
     * @var string|array ready for upload image name
     */
    public $image;
    /**
     * @var array allowed extensions
     */
    private $extensions = array("png", "jpg", "jpeg", "gif");
    /**
     * @var string|array upload errors
     */
    private $uploadErrors = array();
    /**
     * @param $newExt string allowed extensions
     */
    public function addExtension($newExt)
    {
        $this->extensions[] = $newExt;
    }
    /**
     * @param $name file to be uploaded
     * @return $this
     */
    public function setUploadFile($name)
    {
        $this->name = $_FILES[$name]["name"];
        $this->tmp_name = $_FILES[$name]["tmp_name"];
        $this->type = $_FILES[$name]["type"];
        $this->size = $_FILES[$name]["size"];
        $this->errors = $_FILES[$name]["error"];
        return $this;
    }
    /**
     * validation method
     */
    public function validate()
    {
        $this->name = strtolower($this->name);
        $name = explode(".", $this->name);
        // image name
        $this->name = sha1($name[0] . time() . rand());
        // image extension
        $this->ext = array_values(array_slice($name, -1))[0];
        if (!in_array($this->ext, $this->extensions)) {
            $this->uploadErrors[] = "error File type not allowed";
        }
        if ($this->size > 800000) {
            $this->uploadErrors[] = "Image File is too large ";
        }
        if ($this->errors > 0) {
            $this->uploadErrors[] = "error uploading File";
        }
        $this->image = $this->name . "." . $this->ext;
    }
    /**
     * validation method for multiple files
     */
    public function validateMultiple()
    {
        if (is_array($this->name)) {
            for ($i = 0; $i < count($this->name); $i++) {
                $this->name[$i] = strtolower($this->name[$i]);
                $name = explode(".", $this->name[$i]);
                // image name
                $this->name[$i] = sha1($name[0] . time() . rand());
                // image extension
                $this->ext[$i] = array_values(array_slice($name, -1))[0];
                if (!in_array($this->ext[$i], $this->extensions)) {
                    $this->uploadErrors[] = "error File type not allowed";
                }
                if ($this->size[$i] > 50000000) {
                    $this->uploadErrors[] = "Image File is too large";
                }
                if ($this->errors[$i] > 0) {
                    $this->uploadErrors[] = "error uploading File";
                }
                $this->image[$i] = $this->name[$i] . "." . $this->ext[$i];
            }
        }
    }
    /**
     * @return array upload errors
     */
    public function errors()
    {
        return $this->uploadErrors;
    }
    /**
     * @param $path path where to upload file do not use / at the end  it is added by default and you should have permissions to move file
     * @return bool
     */
    public function uploadFile($path)
    {
        try {
            if (!is_writable($path . DS)) throw new \Exception("Insufficient permissions to move file to <b>$path/</b>");
            return (move_uploaded_file($this->tmp_name, $path . DS . $this->image)) ? true : false;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    /**
     * @param string $path place where to upload files do not use / it is added by default and you should have the right permissions
     * @return bool
     */
    public function uploadMultipleFiles($path = "/")
    {
        try {
            if (!is_writable($path . DS)) throw new \Exception("Insufficient permissions to move file to <b>$path/</b>");
            if (is_array($this->image)) {
                for ($i = 0; $i < count($this->image); $i++) {
                    move_uploaded_file($this->tmp_name[$i], $path . DS . $this->image[$i]);
                }
                return true;
            }
            return false;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    /**
     * @param $file file to be deleted
     * @return bool
     */
    public function delete($file)
    {
        if (file_exists($file) && is_writable($file)) {
            unlink($file);
            return true;
        }
        return false;
    }
}