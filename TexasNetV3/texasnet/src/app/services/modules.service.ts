//Service permettant d'activer ou de désactiver des modules dans l'appli
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../services/http-request.service';
import { Subject } from 'rxjs';

@Injectable()
export class ModuleService{
    amountFDPs: number;
    amountFDPSubject = new Subject<number>();
    stockCouleur:boolean;
    stockCouleurSubject=new Subject<boolean>();
    visGalerie:boolean;
    updateAdresse:boolean;
    soColissimo:boolean;
    bfraisDePort:boolean;
    baffLigneVidePanier:boolean;
    bstatValidPanier:boolean;
    nportGratuit:number;
    maintenance:boolean;
    affFiltre:boolean;
    bmodeSaisie:any;
    bfraisDePortJOTT:boolean;
    bcbActiver:boolean;
    bcdeMarque:boolean;
    bCintre:boolean;
    bCGV:boolean;
    bGestionGroupe:boolean;
    bloqueModifParamCompte:boolean;
    bdateBloqPanier:boolean;
    tauxEscompteGlobal:number;
    allModules:any;
    filtreCatalogue:boolean;
    maxQty:any;

    constructor(private httpClient:HttpClient, private httpRequest: HttpRequest){


        this.httpClient.post(this.httpRequest.InfoModules,{
            "cryptKey":"eJhG487711G56D14532Ddgj" //clé de cryptage permettant d'accéder au fichier info-modules.php en post
        }).subscribe(data=>{

            // Vis galerie
                if(data['visGalerie']===0){
                    this.visGalerie = false; //sinon on résolve la promise en false
                }else if(data['visGalerie']===1){
                    this.visGalerie = true; //si visGalerie on resolve en true la promise
                }

            //Update adresse
                if(data['updateAdresse']===0){
                    this.updateAdresse = false;
                }else if(data['updateAdresse']===1){
                    this.updateAdresse =true;
                }
            //SoColissimo
                if(data["soColissimo"]===0){
                    this.soColissimo = false;
                }else if(data["soColissimo"]===1){
                    this.soColissimo = true;
                }

            //FraisDePort
                if(data["fraisDePort"]===0){
                    this.bfraisDePort = false;
                }else if(data["fraisDePort"]===1){
                    this.bfraisDePort = true;
                }

            //affLigneVidePanier
                if(data["affLigneVidePanier"]===0){
                    this.baffLigneVidePanier = false;
                }else if(data["affLigneVidePanier"]===1){
                    this.baffLigneVidePanier = true;
                }

            //StatValidPanier
                if(data["statValidPanier"]===0){
                    this.bstatValidPanier = (false);
                }else if(data["statValidPanier"]===1){
                    this.bstatValidPanier = (true);
                }

            //Maintenance
                if(data["maintenance"]===0){
                    this.maintenance = (false);
                }else if(data["maintenance"]===1){
                    this.maintenance = (true);
                }

            //Filtre de couleur
                if(data["stockCouleur"]===1){
                    this.stockCouleur=true;
                }else{
                    this.stockCouleur=false;
                }

            //Frais de port JOTT
                if(data["fraisDePortJOTT"]===0){
                    this.bfraisDePortJOTT = (false);
                }else if(data["fraisDePortJOTT"]===1){
                    this.bfraisDePortJOTT = (true);
                }

            //CB activer
                if(data["activerVenteCB"]===0){
                    this.bcbActiver = (false);
                }else if(data["activerVenteCB"]===1){
                    this.bcbActiver = (true);
                }

            //CdeMarque
                if(data["cdeMarque"]===0){
                    this.bcdeMarque = (false);
                }else if(data["cdeMarque"]===1){
                    this.bcdeMarque = (true);
                }

            //GestionGroupe
                if(data["gestionGroupe"]===0){
                    this.bGestionGroupe = (false);
                }else if(data["gestionGroupe"]===1){
                    this.bGestionGroupe = (true);
                }

            //Bloquage de la modifications des paramètres de compte
                if(data["bloqueModifParamCompte"]===0){
                    this.bloqueModifParamCompte = (false);
                }else if(data["bloqueModifParamCompte"]===1){
                    this.bloqueModifParamCompte = (true);
                }
            //Bloquage de la modifications de la date lors de la validation du panier
                if(data["dateBloqPanier"]===0){
                    this.bdateBloqPanier = (false);
                }else if(data["dateBloqPanier"]===1){
                    this.bdateBloqPanier = (true);
                }
            //taux d'escompte
              this.tauxEscompteGlobal = data["tauxEscompteGlobal"];


            //PortGratuit
            this.nportGratuit = data["portGratuit"];
        })




        this.visGalerieStatus(); //appel de la fonction au lancement de l'appli
        this.getAmountFDP();
    }

    // Information de tous les modules
    infoModules(){
        return new Promise(
            (resolve,reject)=>{
              if (typeof(this.allModules) === 'undefined') {
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    this.allModules = data;
                    resolve(data);
                });
              } else {
                resolve(this.allModules)
              }
            }
        );
    }


    //visualiser la galerie en fonction de la connexion
    visGalerieStatus(){
        return new Promise(
            (resolve,reject)=>{
                resolve(this.visGalerie)
            }
        )
    }

    //modifier ses coordonnées (oui=>1/true non=>0/false)
    updateAdresseStatus(){
        return new Promise(
            (resolve,reject)=>{
                resolve(this.updateAdresse)
            }
        );
    }

    //bloque la modifications des modifications pour les paramètres du compte (bloque=>1/true, bloque pas=>0/false)
    bloqueModifParamStatus(){
        return new Promise(
            (resolve,reject)=>{
                resolve(this.bloqueModifParamCompte)
            }
        );
    }

    /* Si la commande passe en colissimo */
    soColissiomo(){
        return new Promise(
            (resolve,reject)=>{
                resolve(this.soColissimo)
            }
        );
    }


    /* Si fraisDePort activé */
    fraisDePort(){
        return new Promise(
            (resolve,reject)=>{
                resolve(this.bfraisDePort)
            }
        );
    }

    affLigneVidePanier(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data["affLigneVidePanier"]);
                });
            }
        );
    }

    /* Si fraisDePort activé */
    statValidPanier(){
        return new Promise(
            (resolve,reject)=>{
                resolve(this.bstatValidPanier)
            }
        );
    }

    portGratuit(){
        return new Promise(
            (resolve,reject)=>{
                resolve(this.nportGratuit)
            }
        );
    }


    //Affichage d'une quantité maximum (quantiteMax)
    showMaxQty(){
        return new Promise(
            (resolve,reject)=>{
              if (typeof(this.maxQty)==='undefined') {
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                  this.maxQty = data;
                    resolve(data);
                });
              } else {
                resolve(this.maxQty);
              }
            }
        );
    }

    //Emission de amount FDP
    emitAmountFDP(){
        this.amountFDPSubject.next(this.amountFDPs);
    }

    getAmountFDP(){
        this.httpClient.post(this.httpRequest.InfoModules,{
            "cryptKey":"eJhG487711G56D14532Ddgj"
        }).subscribe(
            (data)=>{

                this.amountFDPs = data["montantPort"];
                this.emitAmountFDP();
            }
        );

    }

    //Affichage l'ordre d'affichage
    displayOrder(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }

    //Affichage l'ordre d'affichage
    ActivateLangEng(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }



    //Affichage des éléments détail produit
    VisualiseInformations(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }

    //Construit un libellé article
    libelleConstruct(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }

    //Construit un libellé article
    ActivatePoints(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }


    stockProduit(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }

    selectionMoment(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }

    promotion(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }


    looks(){
        return new Promise(
            (resolve,reject)=>{
                 this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }

    enMaintenance(){

        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    if(data["maintenance"]===0){
                        this.maintenance = (false);
                    }else if(data["maintenance"]===1){
                        this.maintenance = (true);
                    }
                    resolve(this.maintenance)
                });
            }
        );
    }

    prixVenteConseille(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }

    gestionGroupe(){
        return new Promise(
            (resolve,reject)=>{
                resolve(this.bGestionGroupe)
            }
        );
    }

    CGV(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    if(data["CGV"]===0){
                        resolve(false);
                    }else if(data["CGV"]===1){
                        resolve(true);
                    }
                });
            }
        );
    }

    cintreActif(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    if(data["cintre"]===0){
                        resolve(false);
                    }else if(data["cintre"]===1){
                        resolve(true);
                    }
                });
            }
        );
    }

    fDateBloqPanier(){
        return new Promise(
            (resolve,reject)=>{
                //Bloquage de la modifications de la date lors de la validation du panier
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    if(data["dateBloqPanier"]===0){
                        resolve(false);
                    }else if(data["dateBloqPanier"]===1){
                        resolve(true);
                    }
                });
            }
        );
    }

    btauxEscompteGlobal(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data['tauxEscompteGlobal']);
                });
                //resolve(this.tauxEscompteGlobal)
            }
        );
    }

    cdeMarque(){
        return new Promise(
            (resolve,reject)=>{
                resolve(this.bcdeMarque)
            }
        );
    }


    cbActiver(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    if(data["activerVenteCB"]===0){
                        resolve(false);
                    }else if(data["activerVenteCB"]===1){
                        resolve(true);
                    }
                });
              
            }
        );
    }

    fraisDePortJOTT(){
        return new Promise(
            (resolve,reject)=>{
              if(typeof(this.bfraisDePortJOTT)==='undefined') {
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    if(data["fraisDePortJOTT"]===0){
                        this.bfraisDePortJOTT = false;
                        resolve(false);
                    }else if(data["fraisDePortJOTT"]===1){
                        this.bfraisDePortJOTT = true;
                        resolve(true);
                    }
                });
              } else {
                resolve(this.bfraisDePortJOTT);
              }
            }
        );
    }

    scRetour(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }

    soColissimoInfo(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }

    modeSaisie(){
        return new Promise(
            (resolve,reject)=>{
              if (typeof(this.bmodeSaisie)==='undefined') {
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                  this.bmodeSaisie = data["modeSaisie"];
                  resolve(data["modeSaisie"]);
                });
              } else {
                resolve(this.bmodeSaisie);
              }
            }
        );
    }

    getStockCouleur(){
        this.emitStockCouleur();

    }

    getAfficherFiltres(){
        return new Promise(
            (resolve,reject)=>{
              if (typeof(this.filtreCatalogue)==='undefined') {
                this.httpClient.post(this.httpRequest.InfoModules,{
                    "cryptKey":"eJhG487711G56D14532Ddgj"
                }).subscribe(data=>{
                  if(data["filtreCatalogue"]===0){
                    this.filtreCatalogue = false;
                    resolve(false);
                  }else if(data["filtreCatalogue"]===1){
                    this.filtreCatalogue = true;
                    resolve(true);
                  }
                });
              } else {
                resolve(this.filtreCatalogue);
              }
            }
        );
    }

    emitStockCouleur(){
        this.stockCouleurSubject.next(this.stockCouleur);
    }


}
