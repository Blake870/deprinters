<?php
namespace Swissup\Pagespeed\Model\Css;

class FontDisplay
{

    /**
     *
     * @param string $content
     * @return string
     */
    public function process($content)
    {
        $content = str_replace("\r\n", '', $content);
        $content = preg_replace('!\s+!', ' ', $content);
        $fontFaces = false;
        preg_match_all('/@font-face\s*{(.*?)}/', $content, $fontFaces);

        if (isset($fontFaces[1]) && is_array($fontFaces[1])) {
            foreach ($fontFaces[1] as $fontFace) {
                if (strstr($fontFace, 'font-display:') === false) {
                    $content = str_replace($fontFace, 'font-display:swap;' . $fontFace, $content);
                }
            }
        }

        return $content;
    }
}
