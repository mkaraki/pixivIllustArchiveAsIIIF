# Indexes this application require

```mongo
db.illustmetadatas.createIndex({'tags.name': 1});
db.illustmetadatas.createIndex({'user.id': 1});
```