package com.company;

import com.mongodb.MongoClient;
import com.mongodb.MongoCredential;
import com.mongodb.ServerAddress;
import com.mongodb.client.MongoDatabase;
import java.util.Arrays;

public class Main {
    public static void main(String[] args){

        MongoCredential credential = MongoCredential.createCredential(
            "imse", "globus", "ms3".toCharArray()
        );
        MongoClient mongoClient = new MongoClient(new ServerAddress(
            "185.5.52.148", 27017), Arrays.asList(credential)
        );
        MongoDatabase mongoDatabase = mongoClient.getDatabase("globus");

        if (args.length > 1) {
           switch (args[0]) {
               case "kunde": {
                   Kunde.handle(args, mongoDatabase);
                   break;
               }
               case "mitarbeiter": {
                   Mitarbeiter.handle(args, mongoDatabase);
                   break;
               }
               case "reisebuero": {
                   Reisebuero.handle(args, mongoDatabase);
                   break;
               }
			          case "hotel" : {
                    Hotel.handle(args, mongoDatabase);
                    break;
                }
				        case "reise" : {
                    Reise.handle(args, mongoDatabase);
                    break;
                }
                default:
                    throw new IllegalArgumentException("no such usecase");
           }
        } else {
            throw new IllegalArgumentException("check the arguments");
        }
    }
}
