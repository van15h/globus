package MongoMigration;

import com.mongodb.MongoClient;
import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoDatabase;
import com.mongodb.client.model.Aggregates;
import com.mongodb.client.model.Filters;
import org.bson.Document;
import org.bson.types.ObjectId;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.Arrays;
import java.util.List;

import static com.mongodb.client.model.Filters.eq;

public class MitarbeiterMigration extends Migration {
    public MitarbeiterMigration(Connection connection, MongoClient client, MongoDatabase database) {
        super(connection, client, database);
    }

    @Override
    public void fetch() {


        // migrate to mitarbeiter
        try (Statement statement = this.connection.createStatement()) {
            ResultSet resultSet = statement.executeQuery("SELECT * FROM Mitarbeiter INNER JOIN Person P ON Mitarbeiter.personid = P.id INNER JOIN Reisebuero R ON Mitarbeiter.beschaeftigungRBid = R.id");
            MongoCollection<Document> collection = this.database.getCollection("Mitarbeiter");
            MongoCollection<Document> reiseBueroCollection = this.database.getCollection("Reisebuero");

            while (resultSet.next()) {
                Document document = new Document();
                document.append("personid", resultSet.getString("personid"));
                document.append("steuernummer", resultSet.getString("steuernummer"));
                document.append("gehalt", resultSet.getString("gehalt"));
                document.append("name", resultSet.getString("name"));
                document.append("svnummer", resultSet.getString("SVNummer"));
                document.append("geburtsdatum", resultSet.getString("geburtsdatum"));
                document.append("email", resultSet.getString("email"));
                document.append("reisebuero", resultSet.getString("beschaeftigungRBid"));
                document.append("anschrift",
                    new Document("ort", resultSet.getString("ort"))
                        .append("plz", resultSet.getString("plz"))
                        .append("strasse", resultSet.getString("strasse"))
                );


                collection.insertOne(document);

                ObjectId mitarbeiterId = document.getObjectId("_id");
                String rbid = resultSet.getString("beschaeftigungRBid");
                Document reiseBuero = this.database.getCollection("Reisebuero").aggregate(
                        Arrays.asList(
                            Aggregates.match(eq("rbid", rbid))
                        )
                ).first();

                List<ObjectId> mitarbeiterList = null;
                if (reiseBuero.containsKey("mitarbeiter")) {
                    mitarbeiterList = (List<ObjectId>)reiseBuero.get("mitarbeiter");
                    if (!mitarbeiterList.contains(mitarbeiterId)) {
                        mitarbeiterList.add(mitarbeiterId);
                    }

                } else {

                    mitarbeiterList = Arrays.asList(mitarbeiterId);
                }

                reiseBueroCollection.updateOne(eq("rbid", rbid), new Document("$set", new Document("mitarbeiter", mitarbeiterList)));
            }



        } catch (SQLException e) {
            e.printStackTrace();
        }
    }

    @Override
    public void persist() {

    }
}
