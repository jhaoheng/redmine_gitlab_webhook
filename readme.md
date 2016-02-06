1. 此份工具，在於連接redmine and gitlab間的git，可用於跨平台。
2. 整合git的相關資訊，讓gitlab 與redmine的git repo內容可以同步。
3. 因為利用git mirror，所以會在redmine下同時產生一份備份。

請先確認以下資訊：      
[redmine]
1. redmine的執行目錄，若為docker，請找到安裝並且執行的目錄。
2. 確定 redmine 知道的路徑，並且pwd，取得該路徑。(讓redmine scm可以找到的目錄)
3. copy路徑

[gitlab]
1. gitlab下的repo fork link。請確認可執行。
2. 注意帳密

[redmine 主機 www位置]
1. 將此份文件，放入http下，確認瀏覽器可以看得到。
2. bash setWebHool.sh
3. setWebHook.sh 執行後，會詢問幾個問題：
- token : 
- redmine path : (會在此目的地mirror 一份git，若要分類請設定好資料夾)
- git link : 

4. 完成後，會產生 webhook/[git name folder] 與在redmine的目錄下產生bare.git檔案
5. 裡面檔案
[git name] 
\- config.php
\- webhook.php
\- log\

[redmine]
1. 到redmine的專案下，輸入該bare.git的路徑。