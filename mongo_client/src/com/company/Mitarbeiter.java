package com.company;

import static com.mongodb.client.model.Filters.eq;
import static com.mongodb.client.model.Updates.set;

import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoCursor;
import com.mongodb.client.MongoDatabase;
import org.bson.Document;
import org.bson.types.ObjectId;

public class Mitarbeiter {

  public static void handle(String[] args, MongoDatabase mdb){
    switch (args[1]) {
      case "create": {
        Document doc = new Document()
            .append("steuernummer", args[2])
            .append("gehalt", args[3])
            .append("name", args[4])
            .append("svnummer", args[5])
            .append("geburtsdatum", args[6])
            .append("telefonnummer", args[7])
            .append("email", args[8])
            .append("anschrift",
                new Document("ort", args[9])
                    .append("plz", args[10])
                    .append("strasse", args[11])
            );
        MongoCollection<Document> collection = mdb.getCollection("Mitarbeiter");
        collection.insertOne(doc);
        System.out.println("create successfull");
        break;
      }
      case "read": {
        MongoCollection<Document> collection = mdb.getCollection("Mitarbeiter");
        MongoCursor<Document> cursor = collection.find().iterator();
        try {
          while (cursor.hasNext()) {
            System.out.println(cursor.next().toJson());
          }
        } finally {
          cursor.close();
        }
        break;
      }
      case "update": {
        mdb.getCollection("Mitarbeiter").updateOne(
            eq("_id", new ObjectId(args[2])),
            set("gehalt", args[3]));
        System.out.println("update successfull");
        break;
      }
      case "delete": {
        MongoCollection<Document> collection = mdb.getCollection("Mitarbeiter");
        collection.deleteOne(eq("steuernummer", args[2]));
        System.out.println("delete successfull");
        break;
      }
      default: throw new IllegalArgumentException("unknown command");
    }
  }
}
