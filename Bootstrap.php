<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangyajun <448901948@qq.com>
// +----------------------------------------------------------------------

namespace think\paginator\driver;

use think\Paginator;

/**
 * Bootstrap 分页驱动
 */
class Bootstrap extends Paginator
{

    /**
     * 上一页按钮
     * @param string $text
     * @return string
     */
    protected function getPreviousButton(string $text = "&laquo;"): string
    {

        if ($this->currentPage() <= 1) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url(
            $this->currentPage() - 1
        );

        return $this->getPageLinkWrapper($url, $text);
    }

    /**
     * 下一页按钮
     * @param string $text
     * @return string
     */
    protected function getNextButton(string $text = '&raquo;'): string
    {
        if (!$this->hasMore) {
            return $this->getDisabledTextWrapper($text);
        }

        $url = $this->url($this->currentPage() + 1);

        return $this->getPageLinkWrapper($url, $text);
    }

     //总数标签
     protected  function totalshow()
     {
 
        $totalhtml="<a class=\"disabled\"><span>共".$this->total."条|".$this->lastPage()."页</span></a>";
        return $totalhtml;
 
     }

    // 首页按钮
    protected function pageFirstText(): string
    {
        $pageFirstUrl = $this->url(1);
        return $this->getPageLinkWrapper($pageFirstUrl,'1');
    }

    // 尾页按钮
    protected function pageLastText(): string
    {
        $pageLastUrl = $this->url($this->lastPage());
        return $this->getPageLinkWrapper($pageLastUrl,$this->lastPage);
    }

    /**
     * 页码按钮
     * @return string
     */
    protected function getLinks(): string
    {
        if ($this->simple) {
            return '';
        }

        $block = [
            'first'  => null,
            'slider' => null,
            'last'   => null,
        ];

        $side   = 1;
        $window = $side * 2;

        if ($this->lastPage < $window +1) {
            $block['slider'] = $this->getUrlRange(1, $this->lastPage);
        } elseif ($this->currentPage <= $window-1) {
            $block['slider'] = $this->getUrlRange(1, $window + 1);
        } elseif ($this->currentPage > ($this->lastPage - $window+1)) {
            $block['slider']  = $this->getUrlRange($this->lastPage - ($window), $this->lastPage);
        } else {
            $block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
        }

        $html = '';

        if (is_array($block['first'])) {
      
            $html .= $this->getUrlLinks($block['first']);
        }

        if (is_array($block['slider'])) {
            if ($this->lastPage < 4){
                // 总页码小于4
                $html .= $this->getPreviousButton();
                $html .= $this->getUrlLinks($block['slider']);
                $html .= $this->getNextButton();
            } elseif ($this->lastPage == 4){
                // 总页码等于4
                if($this->currentPage < 3){
                    $html .= $this->getPreviousButton();
                    $html .= $this->getUrlLinks($block['slider']);
                    $html .= $this->pageLastText();
                    $html .= $this->getNextButton();
                } else {
                    $html .= $this->getPreviousButton();
                    $html .= $this->pageFirstText();
                    $html .= $this->getUrlLinks($block['slider']);
                    $html .= $this->getNextButton();
                }
            } elseif ($this->currentPage < 3){
                // 当前页码小于3
                $html .= $this->getPreviousButton();
                $html .= $this->getUrlLinks($block['slider']);
                $html .= $this->getDots();
                $html .= $this->pageLastText();
                $html .= $this->getNextButton();
            } elseif ($this->currentPage >= $this->lastPage - 1){
                // 当前页码 大于等于 总页码减1
                $html .= $this->getPreviousButton();
                $html .= $this->pageFirstText();
                $html .= $this->getDots();
                $html .= $this->getUrlLinks($block['slider']);
                $html .= $this->getNextButton();
            } elseif ($this->currentPage == 3){
                // 当前页码 等于 3
                $html .= $this->getPreviousButton();
                $html .= $this->pageFirstText();
                $html .= $this->getUrlLinks($block['slider']);
                $html .= $this->getDots();
                $html .= $this->pageLastText();
                $html .= $this->getNextButton();
            } elseif ($this->currentPage == $this->lastPage - 2){
                // 当前页码  等于 总页码减2
                $html .= $this->getPreviousButton();
                $html .= $this->pageFirstText();
                $html .= $this->getDots();
                $html .= $this->getUrlLinks($block['slider']);
                $html .= $this->pageLastText();
                $html .= $this->getNextButton();
            } else {
                // 中间其他情况
                $html .= $this->getPreviousButton();
                $html .= $this->pageFirstText();
                $html .= $this->getDots();
                $html .= $this->getUrlLinks($block['slider']);
                $html .= $this->getDots();
                $html .= $this->pageLastText();
                $html .= $this->getNextButton();
            }
        }
        if (is_array($block['last'])) {
            $html .= $this->getUrlLinks($block['slider']);
        }

        return $html;
    }


    //跳转到哪页【分页搜索参数，输入页码跳转】
    protected  function gopage()
    {
        $params = "";
        if(!empty($_REQUEST['page'])){
            //分页进来带参数
            foreach($_REQUEST as $k=>$v){
                if($k == 'page'){
                    $params .="<input class='totpage' type='number' min='1' name='page' value='' placeholder='页码：'>";
                }else{
                    $params .="<input class='totpage' type='hidden' name='' value=''>";
                }
            }
        }else{
            //没分页显示初始
            $params = "<input class='totpage' type='number' min='1' name='page'  value='' placeholder='页码：'>";
        }

        return "<form action='' method='get'><input class='taopage' type='submit' value='跳转'>{$params}</form>";

    }


    /**
     * 渲染分页html
     * @return mixed
     */
    public function render()
    {
        if ($this->hasPages()) {
            if ($this->simple) {
                return sprintf(
                    '<ul class="pager">%s %s</ul>',
                    $this->getPreviousButton(),
                    $this->getNextButton(),
                );
            } else {
                return sprintf(
                    '<div class="pagination">%s %s %s</div>',
                    
                    //第一页
                    // $this->showfirstpage(),
                    //上一页
                    // $this->getPreviousButton(),
                    //页码
                    $this->getLinks(),
                    //下一页
                    // $this->getNextButton(),
                    //最后一页
                    // $this->showlastpage(),
                    //显示数量页码信息
                    $this->totalshow(),
                    $this->gopage(),
                );
            }
        }
    }

    /**
     * 生成一个可点击的按钮
     *
     * @param  string $url
     * @param  string $page
     * @return string
     */
    protected function getAvailablePageWrapper(string $url, string $page): string
    {
        return '<a href="' . htmlentities($url) . '">' . $page . '</a>';
    }

    /**
     * 生成一个禁用的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getDisabledTextWrapper(string $text): string
    {
        return '<a class="disabled"><span>' . $text . '</span></a>';
    }

    /**
     * 生成一个激活的按钮
     *
     * @param  string $text
     * @return string
     */
    protected function getActivePageWrapper(string $text): string
    {
        return '<a class="active"><span>' . $text . '</span></a>';
    }

    /**
     * 生成省略号按钮
     *
     * @return string
     */
    protected function getDots(): string
    {
        return $this->getDisabledTextWrapper('...');
    }

    /**
     * 批量生成页码按钮.
     *
     * @param  array $urls
     * @return string
     */
    protected function getUrlLinks(array $urls): string
    {
        $html = '';

        foreach ($urls as $page => $url) {
            $html .= $this->getPageLinkWrapper($url, $page);
        }

        return $html;
    }

    /**
     * 生成普通页码按钮
     *
     * @param  string $url
     * @param  string    $page
     * @return string
     */
    protected function getPageLinkWrapper(string $url, string $page): string
    {
        if ($this->currentPage() == $page) {
            return $this->getActivePageWrapper($page);
        }

        return $this->getAvailablePageWrapper($url, $page);
    }
}
