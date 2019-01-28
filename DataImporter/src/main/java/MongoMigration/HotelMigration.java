package MongoMigration;

import com.mongodb.MongoClient;
import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoDatabase;
import org.bson.Document;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

public class HotelMigration extends Migration {

    public HotelMigration(Connection connection, MongoClient client, MongoDatabase database) {
        super(connection, client, database);
    }

    @Override
    public void fetch() {
        try (Statement statement = this.connection.createStatement()) {
            ResultSet resultSet = statement.executeQuery("SELECT * FROM Hotel");

            while (resultSet.next()) {
                Document document = new Document();
                document.append("hotelid", resultSet.getString("id"));
                document.append("name", resultSet.getString("name"));
                document.append("sterne", resultSet.getString("sterne"));
                document.append("verpflegung", resultSet.getString("verpflegung"));
                document.append("anschrift",
                        new Document("ort", resultSet.getString("ort"))
                                .append("plz", resultSet.getString("plz"))
                                .append("strasse", resultSet.getString("strasse"))
                );

                MongoCollection<Document> collection = this.database.getCollection("Hotel");
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
