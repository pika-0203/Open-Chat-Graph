<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use Shared\Exceptions\ValidationException;

class FuriganaPageController
{
    function guideline()
    {
        $this->getHtml('https://openchat-jp.line.me/other/guideline', '安心・安全ガイドライン （ふりがな付き）');
    }

    function defamationGuideline()
    {
        $this->getHtml('https://openchat-jp.line.me/other/defamation_guideline_30jga8ku', '誹謗中傷に関する詳しいガイドライン （ふりがな付き）');
    }

    private function getHtml($url, $title)
    {
        try {
            $html = file_get_contents($url);
            if (!$html)
                throw new ValidationException("アクセスエラー\n空のレスポンス");
        } catch (\Throwable $e) {
            throw new ValidationException("アクセスエラー\n" . $e->getMessage());
        }

        $script = <<<JS

document.body.innerHTML = `
<div
  style="
    padding: 4px;
    z-index: 9999;
    background: #f9f9f9;
    font-size: 11px;
    line-height: 1.3;
    text-align: center;
  "
>
  {$title}<br />translated by openchat-reveiw.me<br /><span style="margin-right: 4px;">原文</span><a href="{$url}" target="_blank" style="text-decoration: underline; color: #2196f3;">{$url}</a>
</div>
` + document.body.innerHTML + `
<div style="text-align: center; background: #f9f9f9; font-size: 11px; line-height: 1.3;">
  <span style="display: inline-block; margin-bottom: 2px;">Webサービス by Yahoo! JAPAN （https://developer.yahoo.co.jp/sitemap/）</span>
  <br>※「{$title}」はLINEヤフー社公式のページではありません。
</div>
`

const nodes = Array.from(document.querySelectorAll('body *'))
  .map((el) =>
    Array.from(el.childNodes).filter((node) => {
      if (!(node instanceof Text)) return false
      const value = node.textContent
      const regex = new RegExp('^(?=.*[\u4E00-\u9FFF]).*$')
      return !!regex.test(value)
    })
  )
  .flat()

const obj = nodes.map((node) => node.textContent)
const body = JSON.stringify(obj)

const formData = new FormData()
formData.append('json', body)

const request = new Request('/furigana', {
  method: 'POST',
  body: formData,
})

fetch(request)
  .then((response) => response.json())
  .then((data) => {
    nodes.forEach((node, i) => {
      const parent = node.parentNode;
      const range = document.createRange();
      range.selectNode(node);
      const fragment = range.createContextualFragment(data[i]);
      parent.replaceChild(fragment, node);
    })
  })
  .catch((error) => {
    console.error('Error fetching data:', error)
  })

// ページ内のすべてのiframe要素を取得
const iframes = document.getElementsByTagName('iframe');

// 各iframeに対して処理を行う
for (let i = 0; i < iframes.length; i++) {
    // メッセージイベントリスナーを追加
    window.addEventListener('message', function(event) {
        // iframeからのメッセージのみを処理
        if (event.source === iframes[i].contentWindow) {
          console.log(event)
            // iframeの高さをメッセージのデータに設定
            iframes[i].style.height = event.data.height + 'px';
        }
    });
}

const bodyInner = document.getElementById('___gatsby')
if(bodyInner) {
    bodyInner.style.position = 'relative'
}

const header = document.querySelector('.header')
if (header) {
  header.style.position = 'absolute'
  header.addEventListener('click', () => {
    header.classList.toggle('gnb_open')

    if (header.classList.contains('gnb_open')) {
      document.body.style.overflow = 'hidden'
      header.style.position = 'fixed'
    } else {
        document.body.style.overflow = 'unset'
        header.style.position = 'absolute'
    }
  })
}

JS;

        $cleaned_html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $cleaned_html = preg_replace('/<link\b[^>]*>.*?>/is', '', $cleaned_html);

        $style = <<<CSS
h1,
h2,
h3,
h4,
h5,
h6,
p,
a,
ruby,
ruby > rt {
    font-family: "游ゴシック体", YuGothic, "ヒラギノ角ゴ ProN W3", "Hiragino Kaku Gothic ProN", "メイリオ", Meiryo, Noto Sans JP, "Helvetica Neue", Helvetica, Arial, sans-serif !important;
    letter-spacing: 0;
    line-height: 2;
}

h1,
h2,
h3,
h4,
h5,
h6 {
    font-weight: bold !important;
}
CSS;

        try {
            echo $this->addScriptToBody($cleaned_html, $script, 'https://openchat-jp.line.me', '(ふりがな付き) ', $style);
        } catch (\Throwable $e) {
            throw new ValidationException("ページの取得に失敗しました.\n", $e->message);
        }
    }

    private function addScriptToBody($html, $scriptContent, $baseDomain, $titlePrefix, $style)
    {
        $dom = new \DOMDocument();
        // HTMLを読み込む
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_HTML_NOIMPLIED);
        // scriptタグを作成
        $scriptTag = $dom->createElement('script', $scriptContent);

        // styleタグを作成
        $styleTag = $dom->createElement('style', $style);

        // BODYタグの最後に追加
        $body = $dom->getElementsByTagName('body')->item(0);
        $body->appendChild($scriptTag);
        $body->appendChild($styleTag);

        // base要素を作成して基準URLを設定
        /* $baseTag = $dom->createElement('base');
        $baseTag->setAttribute('href', $baseDomain);
        $head = $dom->getElementsByTagName('head')->item(0);
        $head->appendChild($baseTag); */

        // srcとhref属性を持つ要素を取得
        $elements = $dom->getElementsByTagName('*');
        foreach ($elements as $element) {
            if (($element->hasAttribute('src') || $element->hasAttribute('href')) || $element->getAttribute('data-href')) {
                $srcValue = $element->getAttribute('src');
                $hrefValue = $element->getAttribute('href');
                $hrefValue2 = $element->getAttribute('data-href');
                if (!empty($srcValue) && !filter_var($srcValue, FILTER_VALIDATE_URL) && !preg_match('/data:image/', $srcValue)) {
                    // $srcValueが相対パスの場合の処理
                    $element->setAttribute('src', $baseDomain . $srcValue);
                }
                if (!empty($hrefValue) && !filter_var($hrefValue, FILTER_VALIDATE_URL)) {
                    // $hrefValueが相対パスの場合の処理
                    $element->setAttribute('href', $baseDomain . $hrefValue);
                }
                if (!empty($hrefValue2) && !filter_var($hrefValue, FILTER_VALIDATE_URL)) {
                    // $hrefValueが相対パスの場合の処理
                    $element->setAttribute('href', $baseDomain . $hrefValue2);
                }
            }
        }

        // titleタグを取得・更新
        $title = $dom->getElementsByTagName('title')->item(0);
        if ($title) {
            $title->nodeValue = $titlePrefix . $title->nodeValue;
        }

        // og:titleタグを取得・更新
        // og:imageタグを取得・更新
        $metas = $dom->getElementsByTagName('meta');
        for ($i = 0; $i < $metas->length; $i++) {
            /** @var mixed */
            $meta = $metas->item($i);
            if ($meta->getAttribute('property') === 'og:title') {
                $meta->setAttribute('content', $titlePrefix . $meta->getAttribute('content'));
            }

            if ($meta->getAttribute('property') === 'og:image') {
                $srcValue = $meta->getAttribute('content');
                if (!empty($srcValue) && !filter_var($srcValue, FILTER_VALIDATE_URL)) {
                    // $hrefValueが相対パスの場合の処理
                    $meta->setAttribute('content', $baseDomain . $srcValue);
                }
            }
        }

        // 加工したHTMLの保存
        $newHtml = $dom->saveHTML($dom->documentElement);
        return $newHtml;
    }
}
