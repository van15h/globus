package MongoMigration;


import com.mongodb.MongoClient;
import com.mongodb.client.MongoDatabase;

import java.sql.Connection;

public abstract class Migration {
    protected MongoClient client;
    protected MongoDatabase database;
    protected Connection connection;

    Migration(Connection connection, MongoClient client, MongoDatabase database) {
        this.connection = connection;
        this.client = client;
        this.database = database;
    }

    public void migrate() {
        this.fetch();
        this.persist();
    }

    public abstract void fetch();
    public abstract void persist();

}
