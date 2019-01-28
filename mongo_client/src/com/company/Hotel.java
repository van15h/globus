package com.company;

import com.mongodb.client.MongoCollection;
import com.mongodb.client.MongoCursor;
import com.mongodb.client.MongoDatabase;
import org.bson.Document;
import org.bson.types.ObjectId;

import java.sql.Array;
import java.util.Arrays;

import static com.mongodb.client.model.Filters.eq;
import static com.mongodb.client.model.Updates.set;

public class Hotel {
    public static void handle(String[] args, MongoDatabase mdb) {
        switch (args[1]) {
            case "create": {
                Document doc = new Document()
                        .append("name", args[2])
                        .append("sterne", args[3])
                        .append("verpflegung", args[4])
                        .append("zimmer",
                        new Document ("nummer", args[5])
                        .append("variation", args[6]))
                        .append("anschrift",
                                new Document("ort", args[7])
                                        .append("plz", args[8])
                                        .append("strasse", args[9]));
                MongoCollection<Document> collection = mdb.getCollection("Hotel");
                collection.insertOne(doc);
                System.out.println("create successful");
                break;
            }
            case "read":
            {
                MongoCollection<Document> collection = mdb.getCollection("Hotel");
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

            case "update":{

                mdb.getCollection("Hotel").updateOne(
                        eq("_id", new ObjectId(args[2])),
                        set("sterne", args[3]));
                System.out.println("update successful");
                break;
            }
            case "delete":
            {
                MongoCollection<Document> collection = mdb.getCollection("Hotel");
                collection.deleteOne(eq("_id", new ObjectId(args[2])));
                System.out.println("delete successfull");
                break;

            }
            default: throw new IllegalArgumentException("unknown command");
        }
    }
}
