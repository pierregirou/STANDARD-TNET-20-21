import { HttpClient } from '@angular/common/http';
import { Subject } from 'rxjs';
import { Injectable } from '@angular/core';
import { HttpRequest } from '../services/http-request.service';

@Injectable()

//Service permettant de récupérer les informations du client
export class InformationService{
    update=false; //variable permettant d'indiquer la mise à jour
    constructor(private httpClient:HttpClient, private httpRequest:HttpRequest){}
    private points:number; //contient le nombre de points du client

    ville:string;

    verifyPassword:boolean=false;

    pointSubject = new Subject<number>(); //Nouveau subject pour les points permettant de réagir à de nouvelles informations provenant de la bdd
    userSubject = new Subject<any[]>(); // nouveau subject contenant les infos de l'utilisateur
    recupPoints(login){ //méthode permettant de récupérer les points vers recup-points.php
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.Informations,{"login":sessionStorage.getItem("loginCompte")}).subscribe(data=>{
                    if(data){
                        this.points=data["points"];
                        this.emitPoints();
                    }
                })
            }
        )
    }

    emitPoints(){
        this.pointSubject.next(this.points);
    }

    //Mise à jour des coordonnées 
    updateUser(login,nom,prenom,email,adresse1,adresse2,cp,ville,telephone,langue){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.UpdateUser,{
                    "type":"update",
                    login,
                    nom,
                    prenom,
                    email,
                    adresse1,
                    adresse2,
                    cp,
                    ville,
                    telephone,
                    langue
                }).subscribe(data=>{
                    if(data){
                        this.update=true;
                        resolve(this.update);
                    }else{
                        this.update=false;
                        resolve(this.update);
                    }
                })
            }
        )
    }

    passwordVerify(login,passwordUser){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.UpdateUser,{
                    "type":"verify",
                    login,
                    passwordUser
                }).subscribe(data=>{
                    if(data["success"]){
                        this.verifyPassword=true;
                        resolve(this.verifyPassword)
                    }else{
                        this.verifyPassword=false;
                        resolve(this.verifyPassword);
                    }
                })
            }
        )
    }

    //change mot de passe
    changePassword(newPassword,login){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.UpdateUser,{
                    "type":"change",
                    newPassword,
                    login
                }).subscribe(data=>{
                })
            }
        )
    }

}