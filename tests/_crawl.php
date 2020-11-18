
<?php
// interface for gathering news articles from the web
// RSS is main target, other targets okay
// Copyright (c) 2020 Sami Fouad, edited Oct 2020
/*
     --------- 
    | CBC News | Full List: https://www.cbc.ca/rss/
     ---------
    -> Top Stories (RSS): https://rss.cbc.ca/lineup/topstories.xml 
    -> World (RSS): https://rss.cbc.ca/lineup/world.xml 
    -> Canada (RSS): https://rss.cbc.ca/lineup/canada.xml  
    -> Politics (RSS): https://rss.cbc.ca/lineup/politics.xml  
    -> Business (RSS): https://rss.cbc.ca/lineup/business.xml  
    -> Health (RSS): https://rss.cbc.ca/lineup/health.xml  
    -> Arts & Entertainment (RSS): https://rss.cbc.ca/lineup/arts.xml  
    -> Tech & Science (RSS): https://rss.cbc.ca/lineup/technology.xml 
    -> Offbeat (RSS): https://rss.cbc.ca/lineup/offbeat.xml 
    -> Indigenous (RSS): https://www.cbc.ca/cmlink/rss-cbcaboriginal 
    -> Sports (RSS): https://rss.cbc.ca/lineup/sports.xml 
    -> MLB (RSS): https://rss.cbc.ca/lineup/sports-mlb.xml
    -> NBA (RSS): https://rss.cbc.ca/lineup/sports-nba.xml 
    -> CFL (RSS): https://rss.cbc.ca/lineup/sports-cfl.xml 
    -> NFL (RSS): https://rss.cbc.ca/lineup/sports-nfl.xml 
    -> NHL (RSS): https://rss.cbc.ca/lineup/sports-nhl.xml
    -> Soccer (RSS): https://rss.cbc.ca/lineup/sports-soccer.xml  
    -> Calgary (RSS): https://rss.cbc.ca/lineup/canada-calgary.xml 
    -> Edmonton (RSS): https://rss.cbc.ca/lineup/canada-edmonton.xml
    -> The National (RSS): https://rss.cbc.ca/lineup/thenational.xml    

     ----------
    | CTV News | Full List: https://www.ctvnews.ca/more/rss-feeds-for-ctv-news
     ----------
    -> Top Stories (RSS): http://ctvnews.ca/rss/TopStories 
    -> Canada (RSS): http://ctvnews.ca/rss/Canada 
    -> World (RSS): http://www.ctvnews.ca/rss/World  
    -> Entertainment (RSS): http://www.ctvnews.ca/rss/CTVNewsEnt  
    -> Politics (RSS): http://www.ctvnews.ca/rss/Politics  
    -> Business (RSS): http://www.ctvnews.ca/rss/business/ctv-news-business-headlines-1.867648  
    -> Science & Tech (RSS): http://www.ctvnews.ca/rss/SciTech  
    -> Sports (RSS): http://www.ctvnews.ca/rss/sports/ctv-news-sports-1.3407726  
    -> Health (RSS): http://www.ctvnews.ca/rss/Health  
    -> Autos (RSS): http://www.ctvnews.ca/rss/autos/ctv-news-autos-1.867636  
    -> Calgary (RSS): http://calgary.ctvnews.ca/rss/CalgaryNews
    -> Edmonton (RSS): http://edmonton.ctvnews.ca/rss/EdmontonNews
    
     -------
    | Global | Full List: https://globalnews.ca/pages/feeds/
     -------
    -> Headlines (RSS): https://globalnews.ca/feed/
    -> Global Calgary (RSS): https://globalnews.ca/calgary/feed/
    -> Global Edmonton (RSS): https://globalnews.ca/edmonton/feed/
    -> Canada (RSS): https://globalnews.ca/canada/feed/
    -> World (RSS): https://globalnews.ca/world/feed/
    -> Politics (RSS): https://globalnews.ca/politics/feed/
    -> Money (RSS): https://globalnews.ca/money/feed/
    -> Health (RSS): https://globalnews.ca/health/feed/
    -> Entertainment (RSS): https://globalnews.ca/entertainment/feed/
    -> Environment (RSS): https://globalnews.ca/environment/feed/
    -> Tech (RSS): https://globalnews.ca/tech/feed/   
    -> Sports (RSS): https://globalnews.ca/sports/feed/
    -> Global National (RSS): https://globalnews.ca/national/program/global-national/feed/
    -> The West Block (RSS): https://globalnews.ca/national/program/the-west-block/feed/    

     -------------
    | Toronto Star | Full List: https://www.thestar.com/about/rssfeeds.html
     -------------
    -> Top Stories (RSS): https://www.thestar.com/feeds.topstories.rss
    -> Calgary (RSS): https://www.thestar.com/feeds.articles.calgary.rss
    -> Edmonton (RSS): https://www.thestar.com/feeds.articles.edmonton.rss
    -> Canada (RSS): https://www.thestar.com/feeds.articles.news.canada.rss
    -> World (RSS): https://www.thestar.com/feeds.articles.news.world.rss
    -> Investigations (RSS): https://www.thestar.com/feeds.articles.news.investigations.rss 
    -> Editorials (RSS): https://www.thestar.com/feeds.articles.opinion.editorials.rss
    -> Politics (RSS): https://www.thestar.com/feeds.articles.politics.rss
    -> Business (RSS): https://www.thestar.com/feeds.articles.business.rss
    -> Sports (RSS): https://www.thestar.com/feeds.articles.sports.rss 
    -> Entertainment (RSS): https://www.thestar.com/feeds.articles.entertainment.rss
    -> Lifestyle (RSS): https://www.thestar.com/feeds.articles.life.rss 
    
     ----------------
    | HuffPost Canada |
     ----------------
    -> All Posts (RSS): https://www.huffingtonpost.ca/feeds/index.xml
    
     -------
    | Macleans | https://www.macleans.ca/rss-feeds/
     -------
    -> News (RSS): https://www.macleans.ca/news/feed/
    -> Canada News (RSS): https://www.macleans.ca/news/canada/feed
    -> World News (RSS): https://www.macleans.ca/news/world/feed
    -> Economy (RSS): https://www.macleans.ca/economy/feed/
    -> Arts & Culture (RSS): https://www.macleans.ca/culture/feed/
    -> Politics (RSS): https://www.macleans.ca/politics/feed/
    -> Health (RSS): http://www2.macleans.ca/category/health/feed/
    -> Education (RSS): https://www.macleans.ca/education/feed/
    
     -------
    | Canadaland | https://www.canadalandshow.com (at bottom)
     -------
    -> Main feed (RSS): http://www.canadalandshow.com/feed
    
     -------
    | Outlet |
     -------
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS):  
    
     -------
    | Outlet |
     -------
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS):  
    
     -------
    | Outlet |
     -------
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS):  
    
     -------
    | Outlet |
     -------
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS):  
    
     -------
    | Outlet |
     -------
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
    ->  (RSS): 
*/
?>