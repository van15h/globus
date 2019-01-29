package com.company;

import static com.mongodb.client.model.Filters.eq;
import static com.mongodb.client.model.Updates.set;
import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoCursor;
import com.mongodb.client.MongoDatabase;
import java.util.ArrayList;
import java.util.List;
import org.bson.Document;
import org.bson.types.ObjectId;


public class Reisebuero {
  public static void handle(String[] args, MongoDatabase mdb) throws NullPointerException{
    switch (args[1]) {
      case "create": {
        Document doc = new Document()
            .append("name", args[2])
            .append("kontodaten", args[3])
            .append("anschrift",
                new Document("ort", args[4])
                    .append("plz", args[5])
                    .append("strasse", args[6])
            );

        List<ObjectId> mitarbeiterList = new ArrayList<>();
        System.out.println(args[7]);
        System.out.println(args[8]);
        mitarbeiterList.add(new ObjectId(args[7]));
        mitarbeiterList.add(new ObjectId(args[8]));
        doc.append("mitarbeiter", mitarbeiterList);

        MongoCollection<Document> collection = mdb.getCollection("Reisebuero");
        collection.insertOne(doc);

        System.out.println("create successfull");
        break;
      }
      case "read": {
        MongoCollection<Document> collection = mdb.getCollection("Reisebuero");
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
        mdb.getCollection("Reisebuero").updateOne(
            eq("_id", new ObjectId(args[2])),
            set("kontodaten", args[3]));
        System.out.println("update successfull");
        break;
      }
      case "delete": {
        MongoCollection<Document> collection = mdb.getCollection("Reisebuero");
        collection.deleteOne(eq("_id", new ObjectId(args[2])));
        System.out.println("delete successfull");
        break;
      }
      default: throw new IllegalArgumentException("unknown command");
    }
  }
}
