questions-collector
===================

動作サンプルは[こちら](http://qa.syamn.net/)

スクレイピングのテストプログラムです。
教えてGooへ投稿された質問をエクセルファイルに抽出できます。


DOMの解析にDOMDocumentクラスを利用しているので、with-domでcofigureしていない場合はphp-xml等モジュールが必要です。
エクセルファイルの操作には[PHPExcel](http://phpexcel.codeplex.com/)を利用していますので、ライセンスはこれに準じます(LGPL v2.1)。
