import { HttpClient  } from "@angular/common/http";
import { Injectable } from '@angular/core';
import { ProduitService } from '../services/produits.service';
import { HttpRequest } from '../services/http-request.service';
import { Router } from '@angular/router';
import { ModuleService } from './modules.service'
import { MatSnackBar } from '@angular/material/snack-bar';
@Injectable()
export class AuthService{
    constructor(private snackBar:MatSnackBar, private moduleService:ModuleService, private httpClient : HttpClient, private produitService:ProduitService, private httpRequest: HttpRequest, private router:Router){}
    isAuth=false;
    points=sessionStorage.getItem('points');
    loginCompte:string=sessionStorage.getItem('loginCompte'); //init
    signIn(login,password){ //Fonction qui prend en paramètre le login et le mot de passe depuis le formulaire de connexion
        return new Promise( //retourne une promise sur l'état de la connexion
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.Auth,{
                    login,
                    password
                }).subscribe(data=>{

                    if(data){
                        if(data[0]){ //si la connexion a réussie resolve isAuth et la passe à true
                            this.isAuth=true;
                            if(data[1]=="admin"){
                                this.router.navigate(['/administration']);
                                sessionStorage.setItem("admin","true");
                                sessionStorage.setItem("isLoggedIn","true");
                                sessionStorage.setItem("logWithRepAcc","false");
                            }
                            if(data[1]=="representant"){
                                this.router.navigate(['/representant']);

                                sessionStorage.setItem("representant","true");
                                sessionStorage.setItem("loginRepresentant",login);
                            }
                            if(data[1]=="client"){
                                this.httpClient.post(this.httpRequest.CodeTarif,{
                                    "login":login
                                }).subscribe(data=>{
                                    if(data[0]){
                                        sessionStorage.setItem("codeTarifClient","true");
                                        sessionStorage.setItem("promoPourcentageCodeTarif",data[1]);
                                    }else{
                                        sessionStorage.setItem("codeTarifClient","false");
                                    }
                                })
                                this.moduleService.infoModules().then(
                                    (data2)=>{
                                        if(data2["maintenance"]==1){ //si le site est en maintencance interdit la connexion
                                            resolve(false);
                                        }else{
                                            resolve(this.isAuth);
                                            sessionStorage.setItem("infoClient",JSON.stringify(data[2].infoClient));
                                            sessionStorage.setItem("logWithRepAcc","false");
                                        }
                                    }
                                )
                            }
                            if(data[1]=="approuveur"){
                                this.loginCompte = login;
                                this.router.navigate(['/approuveur/liste']);
                                sessionStorage.setItem("approuveur","true");
                                sessionStorage.setItem("loginCompte",login);
                                sessionStorage.setItem("isLoggedIn","true");
                                sessionStorage.setItem("logWithRepAcc","false");
                            }
                        }else if(!data["success"]){ //si la connexion a échoué resolve isAuth qui est déjà à false
                            this.isAuth=false;
                            resolve(this.isAuth);
                        }
                    }
                })
            }
        )
    }

    logOut(){
        sessionStorage.setItem('isLoggedIn','false'); //passe la localStorage isLoggedIn qui permet de tester la connexion de true à false
        sessionStorage.clear(); //supprime tous les sessionsStorage de l'appli
        this.produitService.recupProduit("FRA");
    }
}
