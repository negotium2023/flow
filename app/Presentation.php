<?php

namespace App;
/**
 * Class provides functionality to replace template variables in a given .pptx file
 * using data provided from an external source.
 *
 *
 */
class Presentation
{
    protected $templateFile;
    protected $slideDirectory = "/ppt/slides/";
    protected $tempDirPath = "";
    protected $outputFilename = "report.pptx";
    protected $payload;

    protected $openTag = '${';
    protected $closeTag = '}';


    /**
     * Primes the instance
     *
     * @param String
     * @return Illuminate/Response
     */
    public function __construct($file = "", $payload) {
        // Set the temporary path
        $this->templateFile = $file;

        $this->payload = $payload;

        // Set the template
        $this->tempDirPath = sys_get_temp_dir()."/". date('U');
    }

    /**
     * Perform the template ETL (Extract, Transform and Load)
     *
     * @return void
     */
    public function run() {

        // Ensure the template has been found and successfully processed
        if ($this->pptxUnzip()) {
            // Isolate the slide directory for looping later
            $slideDirectory = $this->tempDirPath.$this->slideDirectory;

            if(is_dir($slideDirectory)) {

                $directoryLoop = new \DirectoryIterator($slideDirectory);

                foreach ($directoryLoop as $fileinfo) {
                    // Grab every XML file, open it, and do the replacement.
                    if ($fileinfo->getExtension() == 'xml') {
                        // File contents as string
                        $fileContents = file_get_contents($fileinfo->getPathname());

                        /** Provide an associative array of tags and replacements */
                        $client = $this->payload['client'];
if($client['first_name'] != null) {
    $tags = ['client.company' => htmlentities($client['company']),
        'client.first_name' => $client['first_name'],
        'client.last_name' => $client['last_name'],
        'client.email' => $client['email'],
        'client.contact' => $client['contact'],
        'client.cif_code' => $client['cif_code'],
        'client.id_number' => $client['id_number'],
        'client.company_registration_number' => $client['company_registration_number']
    ];
} else {
    $tags = ['client.company' => htmlentities($client['company']),
        'client.first_name' => $client['company'],
        'client.last_name' => $client['last_name'],
        'client.email' => $client['email'],
        'client.contact' => $client['contact'],
        'client.cif_code' => $client['cif_code'],
        'client.id_number' => $client['id_number'],
        'client.company_registration_number' => $client['company_registration_number']
    ];
}
                        $activities = $this->payload['activities'];
                        $count = 1;
                        foreach($activities as $data) {

                            $tags[ 'activity.'.strtolower(str_replace(' ','_',$data['name']))] = $data['data'];
                            $count++;
                        }

                        $related_parties = $this->payload['related_parties'];
                        $count = 1;
                        for($i=0;$i<count($related_parties);$i++) {
                            foreach($related_parties[$i][1] as $data) {

                                if(isset($data['info'])) {
                                    if($data['info']['first_name'] != null) {
                                        $tags['relatedparty' . $i . '.company'] = $data['info']['company'];
                                        $tags['relatedparty' . $i . '.first_name'] = $data['info']['first_name'];
                                        $tags['relatedparty' . $i . '.last_name'] = $data['info']['last_name'];
                                        $tags['relatedparty' . $i . '.cif_code'] = $data['info']['cif_code'];
                                        $tags['relatedparty' . $i . '.id_number'] = $data['info']['id_number'];
                                    } else {
                                        $tags['relatedparty' . $i . '.company'] = $data['info']['company'];
                                        $tags['relatedparty' . $i . '.first_name'] = $data['info']['company'];
                                        $tags['relatedparty' . $i . '.last_name'] = $data['info']['last_name'];
                                        $tags['relatedparty' . $i . '.cif_code'] = $data['info']['cif_code'];
                                        $tags['relatedparty' . $i . '.id_number'] = $data['info']['id_number'];
                                    }
                                }
                                if(isset($data['name'])) {
                                    $tags['relatedparty' . $i . '.activity.' . strtolower(str_replace(' ', '_', $data['name']))] = $data['data'];
                                }
                                $count++;
                            }
                            //dd($tags);
                        }

                        // Loop over tags, replacing each occurence in $fileContents each time.
                        foreach($tags as $tag => $replacement) {
                            $fileContents = str_replace($this->openTag.''.$tag.''.$this->closeTag, $replacement, $fileContents);
                        }


                        // Write the modified string back to file (overwriting it)
                        file_put_contents($fileinfo->getPathname(), $fileContents);
                    }
                }

                // Atomic Zip
                if($this->pptxZip()) {
                    return true;
                }
            }
        }
    }

    /**
     * Unzips pptx file
     *
     * @return boolean unzip success
     */
    private function pptxUnzip() {
        // ZipArchive to unzip the PPTX template file
        $zipHandle = new \ZipArchive;
        if(file_exists(storage_path().'/app/templates/'.$this->templateFile)) {
            $template = $zipHandle->open(storage_path() . '/app/templates/' . $this->templateFile);
        } else {
            $template = $zipHandle->open(storage_path() . '/app/documents/' . $this->templateFile);
        }

        // Create the temporary directory should it not exist.
        if(! is_dir($this->tempDirPath)) {
            mkdir($this->tempDirPath);
        }

        // Extract file to temporary directory
        $zipHandle->extractTo($this->tempDirPath);

        if($zipHandle->close()) {
            return true;
        };
    }

    /**
     * Zips pptx file back up
     */
    private function pptxZip() {

        // Initialize archive object
        $zip = new \ZipArchive();
        $zip->open($this->outputFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->tempDirPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        // Loop over all files and add them to the zip file
        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($this->tempDirPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        if($zip->close()) {
            return true;
        }
    }

    public function getDownloadPath() {
        return $this->outputFilename;
    }

}