package MongoMigration;

import com.mongodb.MongoClient;
import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoDatabase;
import org.bson.Document;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

public class ReiseMigration extends Migration {

    public ReiseMigration(Connection connection, MongoClient client, MongoDatabase database) {
        super(connection, client, database);
    }

    @Override
    public void fetch() {
        try (Statement statement = this.connection.createStatement()) {
            ResultSet resultSet = statement.executeQuery("SELECT * FROM Reise");

            while (resultSet.next()) {
                Document document = new Document();
                document.append("reiseid", resultSet.getString("id"));
                document.append("name", resultSet.getString("name"));
                document.append("einreisedatum", resultSet.getString("einreisedatum"));
                document.append("reisedauer", resultSet.getString("reisedauer"));
                document.append("preis", resultSet.getString("preis"));

                MongoCollection<Document> collection = this.database.getCollection("Reise");
                collection.insertOne(document);
            }

        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void persist() {

    }
}
