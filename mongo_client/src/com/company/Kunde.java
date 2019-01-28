package com.company;

import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoDatabase;
import org.bson.Document;

public class Kunde {

  public static void handle(String[] args, MongoDatabase mdb){
    switch (args[1]) {
      case "create": {
        Document doc = new Document()
            .append("kundenummer", args[2])
            .append("kontodaten", args[3])
            .append("name", args[4])
            .append("sv_nummer", args[5])
            .append("geburtsdatum", args[6])
            .append("telefonnummer", args[7])
            .append("email", args[8])
            .append("anschrift",
                new Document("ort", args[9])
                    .append("plz", args[10])
                    .append("strasse", args[11]));
        MongoCollection<Document> collection = mdb.getCollection("Kunde");
        collection.insertOne(doc);
        break;
      }
      case "read":
      case "update":
      case "delete":
      default: throw new IllegalArgumentException("unknown command");
    }
  }
}
