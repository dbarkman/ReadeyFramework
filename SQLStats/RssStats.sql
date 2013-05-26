SELECT rf.title, count(ri.feed)
FROM rssItems ri
JOIN rssFeeds rf ON rf.uuid = ri.feed
GROUP BY ri.feed;

SELECT rc.name, count(ri.feed)
FROM rssItems ri
JOIN rssFeeds rf ON rf.uuid = ri.feed
JOIN rssCategories rc ON rc.uuid = rf.category
GROUP BY rc.name;
