<?php
namespace JO\JoMuseo\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Mail\MailMessage;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;

class MuseoUtil
{
    public function __construct()
    {

    }

    public function sendMapServiceMail($data = array(), $mailconfig = array(), $templatePath = 'typo3conf/ext/jo_museo/Resources/Private/Templates/Email/Feedback.html')
    {
        /*
        $email = GeneralUtility::makeInstance(FluidEmail::class);
        $email
            ->to($mailconfig['emailSendToAddress'])
            ->from(new Address($mailconfig['emailHostAddress'], $mailconfig['emailHostName']))
            ->subject($mailconfig['emailSubject'])
            ->format('plaintext')
            ->setTemplate()
            ->assign('mySecretIngredient', 'Tomato and TypoScript');
        GeneralUtility::makeInstance(MailerInterface::class)->send($email);
        */
        $data = array_map('trim', $data);
        $data = array_filter($data);
        
        $data['link'] = $_SERVER["HTTP_REFERER"];

        $div = '<p>Objekt: ' . $data['objTitle'] . ' (' . $data['objId'] . ')</p>';
        $div .= '<p>Benutzer: ' . $data['name'] . ' (' . $data['email'] . ')</p>';
        $div .= '<p>Nachricht:<br/> ' . $data['nachricht'] . '</p>';
        $div .= '<br/><br/><br/><p>Link: ' . $data['link'] . '</p>';

        $return = false;
        if (!empty($data) && !empty($mailconfig) && isset($data['objTitle']) && isset($data['nachricht']) && isset($data['email'])) {
            $mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MailMessage::class);
            $return =  $mail
               ->from(new Address($mailconfig['emailHostAddress'], $mailconfig['emailHostName']))
               ->to(new Address($mailconfig['emailSendToAddress'], 'Bearbeiter'))
               ->subject($mailconfig['emailSubject'])
               ->html($div)
               ->send();
        }
        return $return;
    }

    /**
     * @param string $fileName
     * @param string $publicPath
     * @return void
     */
    public function addHeaderFile($fileName, $extensionName, $publicPath = 'Resources/Public/')
    {
        /*
         *            Ausfruf erfolgt hierbei im controlleraction
         *            $this->addHeaderFile('jo.ce001.css');
         *            $this->addHeaderFile('jo.ce001.js');
         */

        $filepath = '';
        $extensionName = "JoMuseo";
        // Pagerenderer instanziieren
        $pageRender = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
        // extkey und extpath ermitteln
        $extkey = GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
        $extPath = ExtensionManagementUtility::extPath($extkey) . $publicPath;
		$include_path = PathUtility::getAbsoluteWebPath($extPath);
		// $extPath = 'typo3conf/ext/jo_museo/Resources/Public/';
        // Dateipfad je nach Endung setzen
        $pi = pathinfo($fileName);

        switch (true) {
            case (strtolower($pi['extension']) == 'css'):
                $filepath = 'Resources/Public/' != $publicPath ? $include_path . $fileName : $include_path . 'Css/' . $fileName;
                // $pageRender->addCssFile($filepath, 'stylesheet', 'all', '', false, false, '', true);
				$pageRender->addCssFile($filepath, 'stylesheet', 'all', '', true, false, '', false);
                break;
            case (strtolower($pi['extension']) == 'js'):
                $filepath = 'Resources/Public/' != $publicPath ? $include_path . $fileName : $include_path . 'JavaScript/' . $fileName;
                // $pageRender->addJsFooterFile($filepath, 'text/javascript', true, false, '', true);
                $pageRender->addJsFooterFile($filepath, 'text/javascript', true, false, '', false);
                break;
        }
    }

    public function readfile_chunked($filename, $retbytes = true)
    {
        $chunksize = 1 * (1024 * 1024);
        $buffer = '';
        $cnt = 0;
        $handle = fopen($filename, 'rb');
        if (false === $handle) return false;
        while (!feof($handle)) {
            $buffer = fread($handle, $chunksize);
            echo $buffer;
            ob_flush();
            flush();
            if ($retbytes) $cnt += strlen($buffer);
        }
        $status = fclose($handle);
        if ($retbytes && $status) return $cnt;
        return $status;
    }
}
