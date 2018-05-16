# Hangmanteau
Hangmanteau is like the original game of hangman only with made-up portmanteau words. <strike>Play on Facebook messenger or as a web application</strike>.

### :poop: What makes a word:
Here word is constituted by the combination of two existing words that have least 2 letters of overlap. For example, `vodkazoo` could be a valid word as it constructible from the real words  `vodka` and `kazoo`. The combination is formed as 'vod' + 'ka' + 'zoo’. Words are generated randomly but are scored to try and produce the most interesting examples.

### Depreciation
This branch has been abandoned. The application in its form here implements a Facebook messenger app which let users play within Facebook messenger. This requires a Mysql database if you want to run it. There is also a web interface available that can be run without database setup, but still requires some setup in the `config.example.php` file and renaming to simply `config.php` .
