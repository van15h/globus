package MongoMigration;

import com.mongodb.MongoClient;
import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoDatabase;
import com.oracle.javafx.jmx.json.JSONFactory;
import org.bson.Document;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

public class KundeMigration extends Migration {

    public KundeMigration(Connection connection, MongoClient client, MongoDatabase database) {
        super(connection, client, database);
    }

    @Override
    public void fetch() {
        try (Statement statement = this.connection.createStatement()) {
            ResultSet resultSet = statement.executeQuery("SELECT * FROM Kunde INNER JOIN Person P ON Kunde.personid = P.id");

            while (resultSet.next()) {
                String name = resultSet.getString("name");
                Document document = new Document();
                document.append("name", resultSet.getString("name"));
                document.append("SVNummer", resultSet.getString("SVNummer"));
                document.append("geburtsdatum", resultSet.getString("geburtsdatum"));
                document.append("kundenNummer", resultSet.getString("kundenummer"));
                document.append("kontoDaten", resultSet.getString("kontodaten"));
                document.append("telefonNummer", resultSet.getString("telefonnummer"));

                System.out.println(name);

                MongoCollection<Document> collection = this.database.getCollection("Kunde");
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
