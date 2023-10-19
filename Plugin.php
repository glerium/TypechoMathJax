<?php
/**
 * TypechoMathJax
 *
 * @package TypechoMathJax
 * @author glerium
 * @version 1.0.0
 */
class TypechoMathJax_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->footer = array(__CLASS__, 'footer');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){
        $tex_font_size = new Typecho_Widget_Helper_Form_Element_Text("tex_font_size", NULL, NULL, _t('公式字体大小（px）'), _t('0表示默认值'));
        $tex_font_size->input->setAttribute('pattern', '[0-9]*');
        $tex_font_size->input->setAttribute('maxlength', '3');
        $tex_font_size->input->setAttribute('oninput', 'removeLeadingZeros()');
        $tex_font_size->value = ltrim($tex_font_size->value, '0');
        $form->addInput($tex_font_size);
        echo '
            <script>
            function removeLeadingZeros() {
                var elements = document.getElementsByName("tex_font_size");
                var input = elements[0];
                input.value = parseInt(input.value);
                if(input.value == "NaN") {
                    input.value = 0;
                }
            }
            </script>
        ';
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 输出尾部js
     *
     * @access public
     * @param unknown $footer
     * @return unknown
     */
    public static function footer() {
        $tex_font_size = Helper::options()->plugin('TypechoMathJax')->tex_font_size;
        
        $set_tex_font_size = '';
        if($tex_font_size) {
            $set_tex_font_size = sprintf('
                var elements = document.getElementsByClassName("MathJax");
                for (var i = 0; i < elements.length; i++) {
                    elements[i].style.fontSize = "%dpx";
                }', $tex_font_size);
        }
        printf('
            <script type="text/x-mathjax-config">
                MathJax.Hub.Config({
                    tex2jax: {
                        inlineMath: [ ["$","$"], ["\\(","\\)"]  ],
                        processEscapes: true,
                        skipTags: ["script", "noscript", "style", "textarea", "pre", "code"]
                    }
                });
                MathJax.Hub.Queue(function() {
                    var all = MathJax.Hub.getAllJax(), i;
                    for(i = 0; i < all.length; i += 1) {
                        all[i].SourceElement().parentNode.className += " has-jax";
                    }
                    %s
                });
            </script>
  
            <script id="mathjax-js" src="//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML"></script>
        ', $set_tex_font_size);
    }
}

