package MongoMigration;

import com.mongodb.MongoClient;
import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoDatabase;
import org.bson.Document;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

public class ReiseBuroMigration extends Migration {

    public ReiseBuroMigration(Connection connection, MongoClient client, MongoDatabase database) {
        super(connection, client, database);
    }

    @Override
    public void fetch() {
        try (Statement statement = this.connection.createStatement()) {
            ResultSet resultSet = statement.executeQuery("SELECT * FROM Reisebuero");

            while (resultSet.next()) {
                Document document = new Document();
                document.append("rbid", resultSet.getString("id"));
                document.append("name", resultSet.getString("name"));
                document.append("kontodaten", resultSet.getString("kontodaten"));
                document.append("anschrift",
                    new Document("ort", resultSet.getString("ort"))
                        .append("plz", resultSet.getString("plz"))
                        .append("strasse", resultSet.getString("strasse"))
                );

                MongoCollection<Document> collection = this.database.getCollection("Reisebuero");
                collection.insertOne(document);
            }

        } catch (SQLException e) {
            e.printStackTrace();
        }

        // ObjectId("5")
        // mitarbeiterId: ObjectId("5")
    }

    @Override
    public void persist() {

    }
}
