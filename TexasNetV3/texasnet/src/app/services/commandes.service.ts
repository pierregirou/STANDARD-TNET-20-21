import { Injectable} from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../services/http-request.service';
import { ModuleService } from '../services/modules.service';
import { Subject } from 'rxjs';
import { MatSnackBar } from '@angular/material/snack-bar';
import { AuthService } from '../services/auth.service';
import { Router } from '@angular/router';
import * as sha1 from 'js-sha1';

@Injectable()
export class CommandeService{
    scSignature:string;
    arrayCommande:any[]=[];
    arrayCommandeSubject=new Subject<any[]>();
    montant:number;
    pieces:number;
    montantEscompte:number;
    isDisabled:boolean=false;
    displayModalPanier:boolean=false; //booleen affichant ou non le modal d'ajout au panier
    displayModalPanierSubject=new Subject<boolean>(); //permet d'émettre la valeur dans l'appli
    displayConfirmBoxSubject=new Subject<boolean>() //permet d'afficher la confirmbox dans l'appli
    displayConfirmBox:boolean=false;
    coRefProduit:string; //pour commande express connaitre la ref du produit
    coRefProduitSubject=new Subject<string>();
    dernierArticleEnPromo:number; //pour commande express connaitre la ref du produit
    dernierArticleEnPromoSubject=new Subject<number>();
    adresseClient:any[]=[];
    idDernierProduitAjout:number; //permet de connaître l'id du dernier produit ajouté au panier
    idDernierProduitAjoutSubject=new Subject<number>();

    scSignatureSubject=new Subject<string>();
    adresseClientSubject=new Subject<any[]>();
    constructor(private router:Router,private authService:AuthService,private httpClient:HttpClient,private httpRequest:HttpRequest, private moduleService:ModuleService,private snack:MatSnackBar){
        this.getCommande();
        this.emitCommande();

    }
    //initialisation de la commande
    initCommande(){
        this.httpClient.post(this.httpRequest.Commandes,{
            'login':sessionStorage.getItem('loginCompte')
        }).subscribe()
    }

    //Ajout d'un produit dans le panier
    submitCommande(coProduit:any[]){

        for(var prop in coProduit){
            const idproduit=`${prop}`;
            const quantite=`${coProduit[prop]}`;
            if((`${coProduit[prop]}`==="")||(`${coProduit[prop]}`==='0')){
            }else{
                this.httpClient.post(this.httpRequest.LigneCommande,{
                    "idproduit":idproduit,
                    "quantite":quantite,
                    "login":sessionStorage.getItem("loginCompte")
                }).subscribe(data=>{});
            }
        }
    }

    getAdresse(){

      this.httpClient.post(this.httpRequest.RecupAdresse,{"login":sessionStorage.getItem("loginCompte")}).subscribe((data:any[])=>{
        this.adresseClient=data;
        this.emitAdresse();
      });
    }

    emitAdresse(){
        this.adresseClientSubject.next(this.adresseClient);
    }

    getCommande(){
        this.httpClient.post(this.httpRequest.RecupCommande,{
            "login":sessionStorage.getItem("loginCompte")
        }).subscribe(
            (data:any[])=>{
                this.arrayCommande=data;

                this.montantEscompte = data[1].escompte;
                this.montant=data[1].montant;
                this.pieces=data[1].pieces;
                if(data[1].montant>0){
                    this.isDisabled=true;
                }else{
                    this.isDisabled=false;
                }

                this.emitCommande();
            }
        );

    }

    emitCommande(){
        this.arrayCommandeSubject.next(this.arrayCommande.slice());
    }

    emitCoRef(){
        this.coRefProduitSubject.next(this.coRefProduit);
    }

    setArticlePromo(enPromo:number) {
      this.dernierArticleEnPromo = enPromo;
      this.emitArticlePromo();
    }

    emitArticlePromo() {
      this.dernierArticleEnPromoSubject.next(this.dernierArticleEnPromo);
    }


    recupCommande(){
        return new Promise(
            (resolve,reject)=>{
                this.httpClient.post(this.httpRequest.RecupCommande,{
                    "login":sessionStorage.getItem("loginCompte")
                }).subscribe(data=>{
                    resolve(data);
                });
            }
        );
    }

    emitDisplayModal(){
        this.displayModalPanierSubject.next(this.displayModalPanier);
    }

    emitDisplayConfirm(){
        this.displayConfirmBoxSubject.next(this.displayConfirmBox);
    }




    ajoutPanier(idproduit:number,quantite:number,codeMarque:string,codeSaison:string,codeColoris:string){
        this.displayModalPanier=false;
        this.emitDisplayModal();

        this.httpClient.post(this.httpRequest.LigneCommande,{
            "idproduit":idproduit,
            "quantite":quantite,
            "login":sessionStorage.getItem("loginCompte"),
            "codeMarque":codeMarque,
            "codeSaison":codeSaison,
            "codeColoris":codeColoris
        }).subscribe(data=>{
            if(data["testMarque"] === "0" ){
                this.snack.open("Impossible d'ajouter cet article au panier","X", {duration: 4000});
                this.displayModalPanier=false;
           // } else if (data["testSaison"] === "0") {
             //   this.snack.open("Impossible d'ajouter cet article au panier. Saison de l'article différente","X", {duration: 4000});
               // this.displayModalPanier=false;
            } else {
                this.idDernierProduitAjout=idproduit;
                this.emitIdDernierProduitAjout();
                this.displayModalPanier=true;
                this.emitDisplayModal();
                this.recupCommande();
                this.emitCommande();
            }

        });

    }

    emitIdDernierProduitAjout(){
        this.idDernierProduitAjoutSubject.next(this.idDernierProduitAjout);
    }

    closeModalPanier(){
        this.displayModalPanier=false;
        this.emitDisplayModal();
    }

    //lorsque l'utilisateur a fini de remplir le formulaire pour la validation de la commande update la commande avec les infos complémentaires
    validCommande(civilite,date,nom,prenom,adresse1,adresse2,cdp,ville,email,telephone,commentaire,pudoFOId,key,trReturnUrlKo,trReturnUrlok,dyForwardingCharges,orderId,numVersion,trClientNumber,signature){
        this.httpClient.post(this.httpRequest.ValidCommande,{
            "pudoFOId":pudoFOId,
            "key":key,
            "trReturnUrlKo":trReturnUrlKo,
            "trReturnUrlok":trReturnUrlok,
            "dyForwardingCharges":dyForwardingCharges,
            "orderId":orderId,
            "numVersion":numVersion,
            "trClientNumber":sessionStorage.getItem("loginCompte"),
            "signature":signature,
            "ceCivility":civilite,
            "ceAdress3":adresse1,
            "ceAdress2":adresse2,
            "ceZipCode":cdp,
            "ceTown":ville,
            "ceEmail":email,
            "cePhoneNumber":telephone,
            "ceName":nom,
            "ceFirstName":prenom,
            "trParamPlus":date,
            "ceDeliveryInformation":commentaire
        }).subscribe(data=>{
                // envoyer le formulaire à colissimo
                const form = document.createElement('form');
                form.action = 'http://ws.colissimo.fr/pudo-fo-frame/storeCall.do';
                form.method = 'post';

                var dataToCrypt = "";
                var keyC = "";
                for (const key in data) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = key;
                    hiddenField.id = key;
                    dataToCrypt=data["pudoFOId"];
                    dataToCrypt+=data["ceName"];
                    dataToCrypt+='';
                    dataToCrypt+=data["dyForwardingCharges"];
                    dataToCrypt+=data["trClientNumber"];
                    dataToCrypt+='';
                    dataToCrypt+=data["orderId"];
                    dataToCrypt+=data["numVersion"];
                    dataToCrypt+=data["ceCivility"];
                    dataToCrypt+=data["ceFirstName"];
                    dataToCrypt+='';
                    dataToCrypt+='';
                    dataToCrypt+=data["ceAdress2"];
                    dataToCrypt+=data["ceAdress3"];
                    dataToCrypt+='';
                    dataToCrypt+=data["ceZipCode"];
                    dataToCrypt+=data["ceTown"];
                    dataToCrypt+='';
                    dataToCrypt+=data["ceDeliveryInformation"];
                    dataToCrypt+=data["ceEmail"];
                    dataToCrypt+=data["cePhoneNumber"];
                    dataToCrypt+='';
                    dataToCrypt+='';
                    dataToCrypt+='';
                    dataToCrypt+='';
                    dataToCrypt+='';
                    dataToCrypt+=data["trParamPlus"];
                    dataToCrypt+=data["trReturnUrlKo"];
                    dataToCrypt+=data["trReturnUrlok"];
                    keyC=data["key"];

                    var sign = sha1(dataToCrypt+keyC);
                    if (key !== "signature") {
                        hiddenField.value = data[key];
                    } else {
                        hiddenField.value = sign;
                    }
                    form.appendChild(hiddenField);
                }

                var inputSignature = document.getElementsByName("signature")[0];
                inputSignature.setAttribute("value", sign);
                document.body.appendChild(form);
                form.submit();

                setTimeout(
                    ()=>{
                        this.initCommande();
                        this.emitCommande();
                        this.getCommande();
                    },10000
                );
            });
    }

    emitSignature(){
        this.scSignatureSubject.next(this.scSignature);
    }

    validCommandeNormal(date,nom,prenom,adresseL,adresseF,libelleServ,commentaire,cintre,montantPanier,montantPort,montantEscompte){

        this.moduleService.cbActiver().then(datas=>{
            if (datas) {
                // si la cb est activer pour OAKWOOD
                /*this.httpClient.post(this.httpRequest.InfoPayementCB,{
                    "login":sessionStorage.getItem("loginCompte"),
                    "nom":nom,
                    "prenom":prenom,
                    "adresseliv":adresseL,
                    "adressefac":adresseF,
                    "libelleServ":libelleServ,
                    "commentaire":commentaire,
                    "cintre":cintre,
                    "montantPort":montantPort,
                    "montantEscompte":montantEscompte,
                    "date":date,
                    "etatInitial":"A valider",
                    "loginRep":sessionStorage.getItem("loginRepresentant")
                }).subscribe(data=>{
                    if (data["success"]) {
                        let listParam:any[]=[];
                            // Formulaire pour payement cb
                        const formCB = document.createElement('form');
                        formCB.action = 'https://paiement.systempay.fr/vads-payment/';
                        formCB.method = 'POST';

                        listParam["vads_action_mode"] = data["vads_action_mode"];
                        listParam["vads_amount"] = data["vads_amount"];
                        listParam["vads_ctx_mode"] = data["vads_ctx_mode"];
                        listParam["vads_currency"] = data["vads_currency"];
                        listParam["vads_page_action"] = data["vads_page_action"];
                        listParam["vads_payment_config"] = data["vads_payment_config"];
                        listParam["vads_site_id"] = data["vads_site_id"];
                        listParam["vads_trans_date"] = data["vads_trans_date"];
                        listParam["vads_trans_id"] = data["vads_trans_id"];
                        listParam["vads_version"] = data["vads_version"];
                        listParam["signature"] = data["signature"];

                        for (const oneParam in listParam) {
                            var currentInput = document.createElement('input');
                            currentInput.type = 'hidden';
                            currentInput.id = oneParam;
                            currentInput.name = oneParam;
                            currentInput.setAttribute('value', listParam[oneParam]);
                            formCB.appendChild(currentInput);
                        }
                        document.body.appendChild(formCB);
                        formCB.submit();
                    }
                });*/
                montantPanier = montantPanier.toFixed(2);
                montantPanier = montantPanier * 100;
                this.httpClient.post(this.httpRequest.InfoPayementCB,{
                    "login":sessionStorage.getItem("loginCompte"),
                    "nom":nom,
                    "prenom":prenom,
                    "adresseliv":adresseL,
                    "adressefac":adresseF,
                    "libelleServ":libelleServ,
                    "commentaire":commentaire,
                    "cintre":cintre,
                    "montantPort":montantPort,
                    "montantPanier":montantPanier,
                    "montantEscompte":montantEscompte,
                    "date":date,
                    "etatInitial":"A valider",
                    "loginRep":sessionStorage.getItem("loginRepresentant")
                }).subscribe(data=>{
                    if (data["success"]) {
                        let listParam:any[]=[];
                            // Formulaire pour payement cb
                        const formCB = document.createElement('form');
                        formCB.action = data["url"];
                        formCB.method = 'POST';

                        listParam["Data"] = data["Data"];
                        listParam["InterfaceVersion"] = data["InterfaceVersion"];
                        listParam["Seal"] = data["Seal"];

                        for (const oneParam in listParam) {
                            var currentInput = document.createElement('input');
                            currentInput.type = 'hidden';
                            //currentInput.id = oneParam;
                            currentInput.name = oneParam;
                            currentInput.setAttribute('value', listParam[oneParam]);
                            formCB.appendChild(currentInput);
                        }
                        document.body.appendChild(formCB);
                        formCB.submit();
                    }
                });
            } else {
                // si CB désactivé
                this.httpClient.post(this.httpRequest.ValidCommandeNormal,{
                    "login":sessionStorage.getItem("loginCompte"),
                    "nom":nom,
                    "prenom":prenom,
                    "adresseliv":adresseL,
                    "adressefac":adresseF,
                    "libelleServ":libelleServ,
                    "commentaire":commentaire,
                    "cintre":cintre,
                    "montantPort":montantPort,
                    "date":date,
                    "etatInitial":"A valider",
                    "loginRep":sessionStorage.getItem("loginRepresentant")
                }).subscribe(data=>{
                    if(data["success"]){
                        if(sessionStorage.getItem("logWithRepAcc")=="true"){
                            sessionStorage.removeItem("loginCompte");
                            sessionStorage.removeItem("infoClient");
                            this.snack.open("La commande a été validée","",{
                                duration:4000
                            });
                            window.open(this.httpRequest.Export+"/export.php?numCmd="+ data['numCommande'] +"",
                            "Mon export",
                            "width=420,height=410,resizable,scrollbars=yes,status=1");
                            this.router.navigate(['/representant']);
                        }
                        setTimeout(
                            ()=>{
                                this.initCommande();
                                this.emitCommande();
                                this.getCommande();
                            },1000
                        );
                    }
                });
            }

        });

    }


    validRetour(civilite,nom,prenom,adresse1,adresse2,cdp,ville,email,telephone,pudoFOId,orderId,signature){
        this.httpClient.post(this.httpRequest.ValidRetour,{
            "orosId":"05462952435457",
            "key":"123456",
            "destinationName":"Inwitex",
            "crName":nom,
            "crFirstName":prenom,
            "crFlagProfessional":"",
            "crCompanyName":"",
            "crSiret":"",
            "crCivility":civilite,
            "crAdress1":adresse1,
            "crAdress2":adresse2,
            "crCountryCode":"FR",
            "crTown":ville,
            "crZipCode":cdp,
            "crTel":telephone,
            "crEmail":email,
            "insuranceRange":"2",
            "flagBulky":"false",
            "returnCause":"",
            "trClientNumber":sessionStorage.getItem("loginCompte"),
            "trProductRef":"",
            "trOrderNumber":orderId,
            "trReturnRef":"",
            "trParamPlus":orderId,
            "orderId":orderId,
            "signature":signature
        }).subscribe(data=>{
                // envoyer le formulaire à colissimo
                const form = document.createElement('form');
                form.action = 'http://www.colissimo.fr/retouronline/storeCall.do';
                form.method = 'post';
                form.name   = 'formretouronlinecall';

                for (const key in data) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = key;
                    hiddenField.value = data[key];
                    form.appendChild(hiddenField);
                }
                document.body.appendChild(form);
                form.submit();
            });
    }

}
