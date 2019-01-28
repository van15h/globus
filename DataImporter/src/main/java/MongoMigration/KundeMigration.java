package MongoMigration;

import com.mongodb.MongoClient;
import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoDatabase;
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
        // migrate to kunde
        try (Statement statement = this.connection.createStatement()) {
            ResultSet resultSet = statement.executeQuery("SELECT * FROM Kunde INNER JOIN Person P ON Kunde.personid = P.id");

            while (resultSet.next()) {
                Document document = new Document();
                document.append("kundennummer", resultSet.getString("kundenummer"));
                document.append("kontodaten", resultSet.getString("kontodaten"));
                document.append("name", resultSet.getString("name"));
                document.append("sv_nummer", resultSet.getString("SVNummer"));
                document.append("geburtsdatum", resultSet.getString("geburtsdatum"));
                document.append("telefonnummer", resultSet.getString("telefonnummer"));
                document.append("email", resultSet.getString("email"));
                document.append("anschrift",
                    new Document("ort", resultSet.getString("ort"))
                        .append("plz", resultSet.getString("plz"))
                        .append("strasse", resultSet.getString("strasse"))
                );

                MongoCollection<Document> collection = this.database.getCollection("Kunde");
                collection.insertOne(document);
            }

        } catch (SQLException e) {
            e.printStackTrace();
        }

        // migrate to mitarbeiter
        try (Statement statement = this.connection.createStatement()) {
            ResultSet resultSet = statement.executeQuery("SELECT * FROM Mitarbeiter INNER JOIN Person P ON Mitarbeiter.personid = P.id");

            while (resultSet.next()) {
                Document document = new Document();
                document.append("steuernummer", resultSet.getString("steuernummer"));
                document.append("gehalt", resultSet.getString("gehalt"));
                document.append("name", resultSet.getString("name"));
                document.append("svnummer", resultSet.getString("SVNummer"));
                document.append("geburtsdatum", resultSet.getString("geburtsdatum"));
                document.append("email", resultSet.getString("email"));
                document.append("anschrift",
                    new Document("ort", resultSet.getString("ort"))
                        .append("plz", resultSet.getString("plz"))
                        .append("strasse", resultSet.getString("strasse"))
                );

                MongoCollection<Document> collection = this.database.getCollection("Mitarbeiter");
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
