# AdelieDebug

XOOPS Cube Legacy でモジュール・プリロードなどを開発するデベロッパー向けのパワフルな開発支援ツールです。

![](https://lh3.googleusercontent.com/-6eASUD-qeYk/TwaPZpHaY9I/AAAAAAAABno/GcZuarntiGM/s400/%2525E3%252582%2525B9%2525E3%252582%2525AF%2525E3%252583%2525AA%2525E3%252583%2525BC%2525E3%252583%2525B3%2525E3%252582%2525B7%2525E3%252583%2525A7%2525E3%252583%252583%2525E3%252583%252588%2525202012-01-06%25252014.14.33.png)

## 特徴

### 1.見やすいデバッグ画面

* タイムライン表示: PHPやSQLのログをすべて、ひとつのタイムラインに集約。処理の前後関係を分かりやすくする工夫。
* エラーをハイライト表示: SQLやPHPのエラーはハイライトで表示されるので視覚的。

### 2.便利なデバッグ関数

* adump(mixed[, mixed, ...]): var_dump()を preタグ で囲んで見やすくした上に、関数が何処に書かれているかまで表示。
* atrace(): 処理経路をトレースします。関数がどこから呼ばれてるから一目瞭然。
* awhich(object または class_name_string): クラスや関数がどのファイルの何行目で定義されているかを表示。
* asynop(object または class_name_string): クラスの実装の概要を表示します。

### 3.XOOPS Cube Legacyのためのツールであること

* もう var_dump() のあとに exit() を付けたり、<{stdout}>をテーマに書く必要はありません。AdelieDebugはXOOPSのob_bufferを自動的に回避するからです。
* XML出力画面にvar_dump()してしまって、Ajaxのテストがうまくできない、なんてことはありません。AdelieDebugはAJAXリクエストのときやHTML出力以外のコンテクストではデバッグ出力を自動でオフにします。

### 4.実践で使われているツールであること

* 開発者が仕事でモジュールを開発するときに使っているツールなのです。

その他詳細な紹介は[XOOPS Cube & TOKYOPenでパワフルなデバッグツールAdelieDebug | Suinasia](http://suin.asia/2012/01/06/xoops-adelie-debug)を御覧ください。


## 基本的な使い方

### インストール

プリロード [AdelieDebug.class.php](https://raw.github.com/suin/xoops-adelie-debug/master/build/AdelieDebug.class.php) をあなたの XOOPS Cube の preload に置くだけです。

wgetでインストールする方法:

```
cd /path/to/your/xoopscube/html/preload
wget https://raw.github.com/suin/xoops-adelie-debug/master/build/AdelieDebug.class.php
```

### アンインストール

不要になった場合は、このプリロードを削除します。

## 設定

### ADELIE_DEBUG_ERROR_REPORTING

定数 `ADELIE_DEBUG_ERROR_REPORTING` を `mainfile.php` にセットすることで、AdelieDebugのエラーレポーティングレベルを調整することができます。この定数がセットされていなければ、`error_reporting(-1)` になります。すなわち、すべてのエラーを通知します。

この設定は通常は使用すべきではありません。しかし、XOOPS Cubeでは、PHP5.3以降の環境で非推奨エラーやストリクトエラーを数多く発生させます。この多数の重要でないエラーは重要なエラーを見えにくくします。そのような開発効率に多くな影響が出る場合に限り、`ADELIE_DEBUG_ERROR_REPORTING` を調整すべきです。

```
	define('ADELIE_DEBUG_ERROR_REPORTING', E_ALL & ~E_DEPRECATED & ~E_STRICT);
```

## コンパイル

以下の情報は、AdelieDebug自体をカスタマイズしたい人にのみ関係します。

### ソースコード

ビルド前のソースコードは source にあります。
ビルド前開発時はシンボリックリンクを使うと便利です。

```
ln -s ~/Projects/xoops-adelie-debug/source/AdelieDebug/Preload.php /var/www/html/preload/AdelieDebug_Preload.class.php
```

### ビルド

コマンドラインで php compile.php を叩くとビルドできます。
ビルドには yuicompressor が必要です。~/bin/yuicompressor-2.4.6.jar に置いてください。

## Tips

### Smartyでadump()を使う

```<{$variable|@adump}>```のようにすると、テンプレートでも変数の中身をダンプすることができます。

