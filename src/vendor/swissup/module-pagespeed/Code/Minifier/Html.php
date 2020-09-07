<?php
namespace Swissup\Pagespeed\Code\Minifier;

class Html extends \Minify_HTML
{
    /**
     *
     * @param  array $m
     * @return string
     */
    protected function _commentCB($m)
    {
        return (0 === strpos($m[1], '[')
            || false !== strpos($m[1], '<![')
            // || false !== strpos($m[1], 'ajaxpro_')
            || false !== stripos($m[1], 'esi <')
            || false !== stripos($m[1], ' fpc')
            || false !== stripos($m[1], ' ko ')
            || false !== stripos($m[1], ' /ko ')
            )
            ? $m[0]
            : '';
    }

    /**
     *
     * @param  array $m
     * @return string
     */
    protected function _removeScriptCB($m)
    {
        $openScript = "<script{$m[2]}";
        $js = $m[3];

        // whitespace surrounding? preserve at least one space
        $ws1 = ($m[1] === '') ? '' : ' ';
        $ws2 = ($m[4] === '') ? '' : ' ';

        // remove HTML comments (and ending "//" if present)
        // if ($this->_jsCleanComments) {
        //     $js = preg_replace('/(?:^\\s*<!--\\s*|\\s*(?:\\/\\/)?\\s*-->\\s*$)/u', '', $js);
        // }

        // remove CDATA section markers
        $js = $this->_removeCdata($js);

        // minify
        $minifier = $this->_jsMinifier
            ? $this->_jsMinifier
            : 'trim';

        $exceptionTypes = [
            'application/ld+json',
            'text/x-custom-template',
            'x-magento-template',
            // 'x-magento-init'
        ];
        foreach ($exceptionTypes as $type) {
            if (false !== stripos($m[2], $type)) {
                $minifier = 'trim';
            }
        }
        try {
            $js = call_user_func($minifier, $js);
        } catch (\Exception $e) {
            // var_dump($e->getMessage());
            // var_dump($e->getTraceAsString());
            // var_dump($js);
            // var_dump($m);
            // die;
            // throw $e;
        }

        return $this->_reservePlace(
            $this->_needsCdata($js)
            ? "{$ws1}{$openScript}/*<![CDATA[*/{$js}/*]]>*/</script>{$ws2}"
            : "{$ws1}{$openScript}{$js}</script>{$ws2}"
        );
    }
}
