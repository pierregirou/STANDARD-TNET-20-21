import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommandeService } from '../../services/commandes.service';
import { Subscription } from 'rxjs';
import { Router } from '@angular/router';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../../services/http-request.service';
import { ImageService } from "../../services/images.service";
import { DeviceDetectorService } from 'ngx-device-detector';
import { ModuleService } from '../../services/modules.service';
import { MatStep, MatStepper } from '@angular/material';
import { BreakpointObserver } from '@angular/cdk/layout';
import { adresseClient } from "../../models/adresseClient.models";
import { DetailService } from '../../services/detail-produit.service';
import { MatSnackBar } from '@angular/material/snack-bar';
import { TemplateService } from '../../services/template.service';
import { isDefined } from '@angular/compiler/src/util';
import { LangueService } from '../../services/langue.service';
import { DomSanitizer } from '@angular/platform-browser';
import { TranslateService,LangChangeEvent } from '@ngx-translate/core';
import { Location } from '@angular/common';

@Component({
  selector: 'app-contenu-panier',
  templateUrl: './contenu-panier.component.html',
  styleUrls: ['./contenu-panier.component.css']
})
export class ContenuPanierComponent implements OnInit, OnDestroy {
  trancheSelect:number = 0;
  selectedOptionL:string = "";
  selectedOptionF:string = "";
  arrayCommandeSubscription:Subscription;
  arrayCommandeSubscriptionFDP:Subscription;
  arrayCommande:any[]=[];
  arrayFraisDePortJott:any[]=[];
  montant:number=0;
  isLinear = false;
  firstFormGroup: FormGroup;
  secondFormGroup: FormGroup;
  formCommandeColissimo:FormGroup;
  formCommandeCDN:FormGroup;
  formCommandeNormal:FormGroup;
  nbPiece:number;
  nbPiecesArt:number;
  isMobile:boolean;
  isDesktop:boolean;
  isTablet:boolean;
  totalPanier:number=0;
  quantitePanier:number=0;
  displayAdresse:boolean; //si updateAdresse=true alors demande à l'utilisateur de modifier son adresse
  soColissimo:boolean; //si la saisie de la commande est de type colissimo
  isValidate:boolean=false; //empecher de sauter une étape stepper
  isEditable:boolean=false;
  tailleEcran:number=1;
  gestionGroupe:boolean;
  montantPort:number=0.00;
  fraisDePort:boolean;
  fraisDePortJOTT:boolean;
  portGratuit:number;
  statValidPanier:boolean;
  amoundFDPSubscription:Subscription;
  nom:string;
  prenom:string;
  email:string;
  adresse1:string;
  adresse2:string;
  cp:string;
  ville:string;
  telephone:string;
  adresseClientL:adresseClient[]=[];
  adresseClientF:adresseClient[]=[];
  adresseClientSubscription:Subscription;
  CGV:boolean;
  CGVpath:any[]=[];
  cintreActif:boolean;
  scFoid:string;
  scFraisExpedition:string;
  scCleSHA1:string;
  scVersionColissimo:string;
  scSignature:string;
  scSignatureSubscription:Subscription;
  coRefProduit:string;
  coProduit:string;
  commandeExpress:boolean;
  coImage:string;
  modeSaisie:number;
  modeleTableau:any[]=[1,2]; //tableau de taille 2 pour afficher en mode tableau les infos plus le tableau des tailles
  //Vérifie l'état de stockCouleur
  stockCouleur:boolean;
  stockCouleurSubscription:Subscription;
  controlStock:boolean; //Vérifie l'état de controlStock
  datePre:string;
  arrayTailleTableau:any[]=[]; //tableau contenant toutes les tailles pour modeSaisie === 2 -->tableau
  tailleDebFin:any[]=[]; //Permet de connaître les tailles pour le tableau des tailles modeSaisie === 2-->tableau
  infoTailleCommandeTableau:any[]=[]; //permet de connaitre les informations sur la taille dans le tableau pour modeSaisie ===2 -->tableau
  libCodeColorisArray:any[]=[];
  libPictoArray:any[]=[];
  stockDisponible:number;
  stockIndisponible:number;
  maxStockLimite:number;
  minStocklimite:number;
  isOptional:boolean=true;
  contenuColorSubscription:Subscription;
  contenuColor:string;
  codeTarifClient:boolean;
  promoPourcentageCodeTarif:number;
  promoPourcentage:number;
  promoMontant:number;
  imageArt:string;
  langueSelect:number; //permet de connaître la langue choisie
  langueSelectSubscription:Subscription; //subscription de langue select depuis le service langue
  quantiteParLigne:any[]=[];
  afficherDetails:boolean[]=[];
  detailsDefaut:boolean=false;
  totalPanierPlusFDP:number=0.00;
  montantEscompte:number=0.00;
  montantNetTPH:number=0.00;
  montantTVA:number=0.00;
  tauxEscompte:number=0.00;
  dateBloqPanier:boolean;
  indexCurrentDetails:number;
  imgEnCours:string[]=[];
  sommeArrayColoriSelection:number=0;
  codeFiscal:number;
  forceAffichageMobile:boolean = false;
  fiscalFrancais:number;
  quantiteInsuf:string;
  pasDeStock:string;
  valueAreaComment:string;

  constructor(private templateService:TemplateService,private snackBar:MatSnackBar,private detailService:DetailService, private breakPoint:BreakpointObserver, private moduleService:ModuleService,private deviceService:DeviceDetectorService,public imageService:ImageService,private httpRequest:HttpRequest,private commandeService:CommandeService, private router:Router, private formBuilder:FormBuilder, private httpClient:HttpClient, private langueService:LangueService, private sanitizer:DomSanitizer,translate: TranslateService, private location:Location) {
    translate.get('panier.quantiteInsuf').subscribe((res: string) => {
      this.quantiteInsuf = res;
    });

    translate.get('panier.noStock').subscribe((res: string) => {
      this.pasDeStock = res;
    });

    translate.onLangChange.subscribe((event: LangChangeEvent) => {
      translate.get('panier.quantiteInsuf').subscribe((res: string) => {
        this.quantiteInsuf = res;
      });

      translate.get('panier.noStock').subscribe((res: string) => {
        this.pasDeStock = res;
      });

    });

    this.commandeService.recupCommande();
    breakPoint.observe([
      '(max-width: 1050px)'
    ]).subscribe(result => {
      if (result.matches) {
        this.modeSaisie = 1;
        this.forceAffichageMobile = true;
      }else{
        this.modeSaisie = 2;
      }
    });

    this.httpClient.post(this.httpRequest.InfoUser,{"login":sessionStorage.getItem("loginCompte")}).subscribe(data=>{
      this.nom=data["nom"];
      this.prenom=data["prenom"];
      this.ville=data["ville"];
      this.email=data["email"];
      this.telephone=data["telephone"];
      this.cp=data["cp"];
      this.adresse1=data["adresse1"];
      this.adresse2=data["adresse2"];
    });

    this.moduleService.soColissimoInfo().then(
      data=>{
        this.scFoid = data["scFoid"];
        this.scFraisExpedition = data["scFraisExpedition"];
        this.scCleSHA1 = data["scCleSHA1"];
        this.scVersionColissimo = data["scVersionColissimo"];
    });

    this.arrayCommandeSubscriptionFDP=this.commandeService.arrayCommandeSubject.subscribe(
      (data:any[])=>{
        this.quantitePanier = data[1].pieces
        this.calculFDP();
      });

      //si CGV activé
      this.moduleService.CGV().then(
        (data:boolean)=>{
          this.CGV=data;
        }
      );

      //si cintre activé
      this.moduleService.cintreActif().then(
        (data:boolean)=>{
          this.cintreActif=data;
        }
      );


   }

   calculFDP(){
     if(this.quantitePanier < 1) {
      this.arrayCommandeSubscription=this.commandeService.arrayCommandeSubject.subscribe(
        (data:any[])=>{
          this.quantitePanier = data[1].pieces
        });
     }
     this.moduleService.fraisDePort().then(
      (data:boolean)=>{
        this.fraisDePort=data;
      }
    );

    let infoClient = JSON.parse(sessionStorage.getItem("infoClient"));
    this.codeFiscal = infoClient.codeFiscal;

    if (this.codeFiscal === this.fiscalFrancais ){
      this.montantTVA = Number(this.totalPanier) * 0.2;
      this.totalPanierPlusFDP = Number(this.totalPanier)  * 1.2;
    }

    //updateCDE montant Port
    this.httpClient.post(this.httpRequest.updateCommande,{
      "login":sessionStorage.getItem("loginCompte"),
      "montantEscompte":this.totalPanier * (this.tauxEscompte/100),
      "montantPort":this.montantPort,
      "montantTPH":this.montantNetTPH,
      "montantTVA":this.montantTVA
    }).subscribe(data=>{
    });


   /*this.httpClient.post(this.httpRequest.InfoFDPJOTT,{
        "login":sessionStorage.getItem("loginCompte")
      }).subscribe(datas=>{

        for(let i = 0; i < Object.keys(datas).length;i++) {
          if(this.quantitePanier >= Number(datas[i].trancheDeb) && this.quantitePanier <= Number(datas[i].trancheFin)) { // le mettre pour les quantités
          //if(this.totalPanier >= Number(datas[i].trancheDeb) && this.totalPanier <= Number(datas[i].trancheFin)) {
            if(datas[i].calculer < 1) {
              this.montantPort = Number(datas[i].montantFDP);
            } else {
              this.montantPort = Number(this.quantitePanier * datas[i].montantFDP) ;
            }
          }

        }

        let infoClient = JSON.parse(sessionStorage.getItem("infoClient"));
        this.codeFiscal = infoClient.codeFiscal;
        if(this.tauxEscompte > 0){
          this.montantNetTPH = ((this.totalPanier - this.montantEscompte) * 0.000675);
          this.totalPanierPlusFDP = (((this.totalPanier * 1-this.montantEscompte) * 1.000675)+Number(this.montantPort));
          this.montantTVA = (this.totalPanierPlusFDP) * 0.2
          this.totalPanierPlusFDP = this.totalPanierPlusFDP * 1.2;
        } else {
          if (this.montantPort > 0 ) {
            this.totalPanierPlusFDP = Number(this.totalPanier)+Number(this.montantPort);
          } else {
            this.totalPanierPlusFDP = this.totalPanier
          }
        }


        if (this.codeFiscal === this.fiscalFrancais ){
          console.log("echo")
          this.montantTVA = (this.totalPanierPlusFDP) * 0.2 //0.055 pour inwimed
          this.totalPanierPlusFDP = (this.totalPanierPlusFDP)  * 1.2;
        }

        //updateCDE montant Port
        this.httpClient.post(this.httpRequest.updateCommande,{
          "login":sessionStorage.getItem("loginCompte"),
          "montantEscompte":this.totalPanier * (this.tauxEscompte/100),
          "montantPort":this.montantPort,
          "montantTPH":this.montantNetTPH,
          "montantTVA":this.montantTVA
        }).subscribe(data=>{
        });


      });*/



  }

  ngOnInit() {

    let infoClient = JSON.parse(sessionStorage.getItem("infoClient"));
    this.codeFiscal = infoClient.codeFiscal;
    this.httpClient.post(this.httpRequest.InfoParametrages,{
      "parametrages":"getParam"
    }).subscribe(data=>{
      this.fiscalFrancais=data[1].fiscalFrancais;
    })

    this.arrayCommandeSubscriptionFDP=this.commandeService.arrayCommandeSubject.subscribe(
      (data:any[])=>{
        this.quantitePanier = data[1].pieces
        this.calculFDP();
      });

    this.moduleService.btauxEscompteGlobal().then(
      (data:number)=>{
        this.tauxEscompte = data;
        this.montantEscompte = this.totalPanier * (this.tauxEscompte/100);
    });

    var now = new Date();
    var annee   = now.getFullYear();
    var mois    = now.getMonth() + 1;
    var jour    = now.getDate();
    this.datePre = mois +"/"+jour+"/"+annee;

    /* Choix langue */
    this.langueSelectSubscription=this.langueService.langueSelectSubject.subscribe(
      (selectLangue:number)=>{
        this.langueSelect=selectLangue;
      }
    );
    this.langueService.emitLangueSelect();

    //si CGV activé
    this.moduleService.CGV().then(
      (data:boolean)=>{
        this.CGV=data;
      }
    );

    //si cintre activé
    this.moduleService.cintreActif().then(
      (data:boolean)=>{
        this.cintreActif=data;
      }
    );

    //si bloqPanier activé
    this.moduleService.fDateBloqPanier().then(
      (data:boolean)=>{
          this.dateBloqPanier=data;
      }
    );

    this.httpClient.post(this.httpRequest.MontantSite,{
      "login":sessionStorage.getItem("loginCompte")
    }).subscribe(data=>{
      this.promoPourcentage=Number(data[1]);
      this.promoMontant=Number(data[2]);
    });


    if(sessionStorage.getItem("codeTarifClient")==="true"){
      this.codeTarifClient=true;
      this.promoPourcentageCodeTarif=Number(sessionStorage.getItem("promoPourcentageCodeTarif"));
    }else{
      this.codeTarifClient=false;
    }

    this.templateService.getContenuColor();
    this.contenuColorSubscription=this.templateService.contenuColorSubject.subscribe(
      (contenuColor:string)=>{
        this.contenuColor='#'+contenuColor;
      }
    );
    this.templateService.emitContenuColor();

    this.moduleService.getStockCouleur();
    this.stockCouleurSubscription=this.moduleService.stockCouleurSubject.subscribe(
      (stockCouleur:boolean)=>{
        this.stockCouleur=stockCouleur
      }
    );
    this.moduleService.emitStockCouleur();
    this.moduleService.stockProduit().then(
      (data)=>{
        this.stockDisponible=data["stockDisponible"];
        this.stockIndisponible=data["stockIndisponible"];
        this.maxStockLimite=data["maxStockLimite"];
        this.minStocklimite=data["minStockLimite"];
        if(data["controleStock"]==0){
          this.controlStock=false;
        }else{
          this.controlStock=true;
        }
      }
    )

    if(sessionStorage.getItem("isLoggedIn")!==null){

      this.scSignatureSubscription=this.commandeService.scSignatureSubject.subscribe(
        (data:string)=>{
          this.scSignature=data;
        }
      );
      this.commandeService.emitSignature();

      this.adresseClientSubscription=this.commandeService.adresseClientSubject.subscribe(
        (data:any[])=>{
          for(let prop in data){
            var type:string="";
            var adresse1:string="";
            var adresse2:string="";
            var codePostal:string="";
            var numero:string="";
            var pays:string="";
            var ville:string="";
            var id:string="";
            type=prop;
            if (type === "livraison"){
              for(let prop2 in data[prop]){
                adresse1=data[prop][prop2]["adresse1"];
                adresse2=data[prop][prop2]["adresse2"];
                codePostal=data[prop][prop2]["codePostal"];
                numero=data[prop][prop2]["numero"];
                pays=data[prop][prop2]["pays"];
                ville=data[prop][prop2]["ville"];
                id= data[prop][prop2]["id"];

                if (this.selectedOptionL === "") {
                  this.selectedOptionL = id;
                }
                this.adresseClientL.push(new adresseClient(type,adresse1,adresse2,codePostal,numero,pays,ville,id));
              }
            }
            if (type === "facturation"){
              for(let prop2 in data[prop]){
                adresse1=data[prop][prop2]["adresse1"];
                adresse2=data[prop][prop2]["adresse2"];
                codePostal=data[prop][prop2]["codePostal"];
                numero=data[prop][prop2]["numero"];
                pays=data[prop][prop2]["pays"];
                ville=data[prop][prop2]["ville"];
                id=data[prop][prop2]["id"];

                if (this.selectedOptionF === "") {
                  this.selectedOptionF = id;
                }

                this.adresseClientF.push(new adresseClient(type,adresse1,adresse2,codePostal,numero,pays,ville,id));
              }
            }

          }
        }
      );

      this.commandeService.getAdresse();

      //si GestionGroupe activé
      this.moduleService.gestionGroupe().then(
        (data:boolean)=>{
          this.gestionGroupe=data;
        }
      );

      //si statValidPanier activé
      this.moduleService.statValidPanier().then(
        (data:boolean)=>{
         this.statValidPanier=data;
        }
      );

      //si frais de ports activé
      this.moduleService.fraisDePort().then(
        (data:boolean)=>{
          this.fraisDePort=data;
        }
      );

      //si frais de ports JOTT activé
      this.moduleService.fraisDePortJOTT().then(
        (data:boolean)=>{
          this.fraisDePortJOTT=data;

          this.calculFDP();
        }
      );


      //déterminer port gratuit
      this.moduleService.portGratuit().then(
        (data:number)=>{
          this.portGratuit=data;
        }
      )
        this.amoundFDPSubscription=this.moduleService.amountFDPSubject.subscribe(
          (data:number)=>{
            if (this.montantPort < 1) {
              this.montantPort = data;
            } else {
              this.montantPort = this.montantPort;
            }
          }
        );
        this.moduleService.getAmountFDP();

        /* Récupère le mode de saisie 1-->ligne 2-->tableau */
        this.moduleService.modeSaisie().then(
          (modeSaisie:number)=>{
            this.modeSaisie=Number(modeSaisie);
            this.forceAffichageMobile ? this.modeSaisie = 1 : this.modeSaisie = Number(modeSaisie);
            this.arrayCommandeSubscription=this.commandeService.arrayCommandeSubject.subscribe(
              (data:any[])=>{
                if(isDefined(data[1])){
                  this.nbPiece=Number(data[1].pieces);
                    if(isDefined(data[2])){
                      var arrayTab:any[];
                      this.arrayCommande=[];
                      this.imgEnCours=[];
                      for(let i=0;i< Object.keys(data[2]).length;i++){
                        arrayTab=[];
                        arrayTab["info"]=Object.entries(data[2][i]);
                        //arrayTab["tableau"]=[];
                        this.arrayCommande.push(arrayTab);
                        if (this.imgEnCours[i] === '' || this.imgEnCours[i] === undefined) {
                          for (let uneCouleur of arrayTab["info"][17][1]) {
                            if (uneCouleur[0] === arrayTab["info"][18][1]) {
                              this.imgEnCours[i] = `${this.imageService.PhotosArt}/${uneCouleur[4]}`;
                              break;
                            } else {
                              this.imgEnCours[i] = `${this.imageService.PhotosArt}/${arrayTab["info"][13][1]}`;
                            }
                          }
                        }
                        if(this.afficherDetails.length < Object.keys(data[2]).length) this.afficherDetails.push(this.detailsDefaut);
                        /* Permet de créer le tableau des tailles commande pour le cas d'un modeSaisie===2 -->tableau */
                        this.detailService.getDetail(data[2][i].refproduit).then(
                          (data2:any[])=>{//récupère les détails du produit
                              // Récupère les quantités par ligne pour l'affichage du panier
                            for(let k = 0; k < data2[4].length; k++) {
                              let currentTab = data2[4][k];
                              let lastElem = currentTab.length-1;
                              if(typeof(this.quantiteParLigne[i])==='undefined'){this.quantiteParLigne[i]=[]};
                              this.quantiteParLigne[i].push(currentTab[lastElem].quantiteLigne);
                            }
                            this.arrayTailleTableau[i]=data2[1].libcoloris;

                            this.libCodeColorisArray[i]=data2[1].codeColoris;
                            this.libPictoArray[i]=data2[1].pictogramme;
                            this.infoTailleCommandeTableau[i]=data2[4];
                            var lengthArray = this.infoTailleCommandeTableau[i].length;
                            this.moduleService.showMaxQty().then(data3=>{
                              if(data3["quantiteMax"]>0){ // si quantiteMax est à 1 on cache le stock
                                for(let j=0;j<lengthArray;j++){
                                  var lengthArray2=this.infoTailleCommandeTableau[i][j].length;
                                  for(let k=0;k<lengthArray2;k++){
                                    if(this.infoTailleCommandeTableau[i][j][k].stockdisponible>data3["valQteMax"]){
                                      this.infoTailleCommandeTableau[i][j][k].stockdisponible=data3["valQteMax"];
                                    }
                                  }
                                }
                              }
                            })
                            /* Permet de connaître toutes les tailles du produit */
                            var taille:any[]=[];
                            for(let j=0;j<Object.keys(data2[4][0]).length;j++){
                              taille[j]=data2[4][0][j].taille;
                            }
                            this.tailleDebFin[i]=taille;
                          }
                        )
                      }
                    }

                  this.montantEscompte=data[1].escompte;
                  this.quantitePanier=data[1].pieces;
                  this.totalPanier=data[1].montant;
                  //this.montantTVA=data[1].mttva;   ////////////////////////////////     rmettre la   TVA           /////////////////////////////////////
                  this.montantPort=Number(data[1].fraisport);
                  this.montantNetTPH=data[1].mtTPH;
                  this.totalPanierPlusFDP= (data[1].montant-Number(data[1].escompte)+Number(this.montantPort)+Number(data[1].mtTPH))
                  //this.totalPanierPlusFDP= (data[1].montant-Number(data[1].escompte)+Number(this.montantPort)+Number(data[1].mtTPH))*1.055
                }
              }
            );
            this.commandeService.getCommande();
          }
        )
    }
    this.firstFormGroup = this.formBuilder.group({
      firstCtrl: ['', Validators.required]
    });
    this.secondFormGroup = this.formBuilder.group({
      secondCtrl: ['', Validators.required]
    });
    this.isDesktop=this.deviceService.isDesktop();
    this.isMobile=this.deviceService.isMobile();
    this.isTablet=this.deviceService.isTablet();
    this.moduleService.updateAdresseStatus().then(
      (data:boolean)=>{
        this.displayAdresse=data;
      }
    );

    this.moduleService.soColissiomo().then(
      (data:boolean)=>{
        this.soColissimo=data;
      }
    )
          //si bloqPanier activé
          this.moduleService.fDateBloqPanier().then(
            (data : boolean)=>{
              this.dateBloqPanier=data;
            });

    this.initFormNormal();
    this.initFormColissimo();

      // Défini les liens des CGV selon la langue : /!\ l'index correspond à la variable 'langueSelect' /!\
    this.CGVpath[1] = this.sanitizer.bypassSecurityTrustResourceUrl(this.imageService.Documents+"/CGV_FRA.pdf");
    this.CGVpath[2] = this.sanitizer.bypassSecurityTrustResourceUrl(this.imageService.Documents+"/CGV_ENG.pdf");

    if (this.breakPoint.isMatched('(max-width: 1050px)')) {
      this.modeSaisie = 1;
      this.forceAffichageMobile = true;
    } else {
      this.modeSaisie = 2;
    }
  }

  returnProducts(){
    //this.router.navigate(['/contenu/produits']);
    this.location.back()
  }

  ngOnDestroy(){
    this.arrayCommandeSubscription.unsubscribe();
    this.arrayCommandeSubscriptionFDP.unsubscribe();
  }

  quantitePlus(id:number,quantite:number){
    if(this.fraisDePortJOTT){
      this.calculFDP();
    }
    this.httpClient.post(this.httpRequest.UpdatePanier,{
      "login":sessionStorage.getItem("loginCompte"),
      "idproduit":id,
      "action":"plus",
      "quantite":quantite,
      "fraisDePort":this.montantPort
    }).subscribe(data=>{
      if(this.modeSaisie===1){
        this.commandeService.getCommande();
      }
      if(this.modeSaisie===2){
        this.changeArrayCommandeTab();
      }
    })
  }

  quantiteMinus(id:number,quantite:number){
    if(this.fraisDePortJOTT){
      this.calculFDP();
    }
    this.httpClient.post(this.httpRequest.UpdatePanier,{
      "login":sessionStorage.getItem("loginCompte"),
      "idproduit":id,
      "action":"minus",
      "quantite":quantite,
      "fraisDePort":this.montantPort
    }).subscribe(data=>{
      if(this.modeSaisie===1){
        this.commandeService.getCommande();

      }
      if(this.modeSaisie===2){
        this.changeArrayCommandeTab();
      }
    })
  }

  onChangeQuantite(quantite:number,idproduit:string){
    if(this.fraisDePortJOTT){
      this.calculFDP();
    }
    var quantiteChoix=quantite+1
    this.httpClient.post(this.httpRequest.UpdatePanier,{
      "login":sessionStorage.getItem("loginCompte"),
      'idproduit':idproduit,
      'action':'updateMobile',
      "quantite":quantiteChoix,
      "fraisDePort":this.montantPort
    }).subscribe(data=>{
      if(this.modeSaisie===1){
        this.commandeService.getCommande();
      }
    })
  }

  onDelete(idproduit:string, quantite:any){
    if(this.fraisDePortJOTT){
      this.calculFDP();
    }
    if(this.arrayCommande.length<2 && this.modeSaisie===1){
      this.onConfirmDeleteAll();
    }else{
      this.httpClient.post(this.httpRequest.UpdatePanier,{
        "login":sessionStorage.getItem("loginCompte"),
        "idproduitTab":idproduit,
        "action":"delete",
        "quantite":quantite,
        "fraisDePort":this.montantPort
      }).subscribe(data=>{
        if(this.modeSaisie===1){
          this.imgEnCours=[];
          this.commandeService.getCommande();
        }
        if(this.modeSaisie===2){
          this.changeArrayCommandeTab();
        }
      })
    }
  }

  onDeleteProduitTab(infoCommandeTab:any, prixASup:any){
    console.log(infoCommandeTab,prixASup);
    let idsToDelete = [];
    let idsToDelString = '';
    let prixCommande = 0;
    for (let uneCom of infoCommandeTab) {
      prixCommande = uneCom[uneCom.length-1].montantLigne/uneCom[uneCom.length-1].quantiteLigne;
      for (let i = 0;i < uneCom.length-1; i++) {
        if (uneCom[i].quantite > 0 && prixCommande === Number(prixASup)) {
          idsToDelete.push(uneCom[i].idproduit);
        }
      }
    }

    for (let j = 0; j < idsToDelete.length; j++) {
      j !== idsToDelete.length-1 ? idsToDelString += `'${idsToDelete[j]}',` : idsToDelString += `'${idsToDelete[j]}'`
    }
    this.onDelete(idsToDelString,0);
  }

  onConfirmDeleteAll(){
    this.commandeService.displayConfirmBox=true;
    this.commandeService.emitDisplayConfirm();
  }


  initFormColissimo(){
    this.formCommandeColissimo=this.formBuilder.group({
      scFoid:'',
      scCleSHA1:'',
      scURLOK:'',
      scURLKO:'',
      scFraisExpedition:'',
      scOrderID:'',
      scVersionColissimo:'',
      scNumClient:'',
      scSignature:'',
      genre:'',
      date:['',Validators.required],
      nom:['',[Validators.required,Validators.pattern('[a-zA-Zéèëêîï -_]{1,}')]],
      prenom:['',[Validators.required,Validators.pattern('[a-zA-Zéèëêîï -_]{1,}')]],
      adresse1:['',Validators.required],
      adresse2:'',
      cdp:['',[Validators.required,Validators.pattern('[0-9]{5}')]],
      ville:['',[Validators.required,Validators.pattern('[a-zA-Zéèëêîï -_]{1,}')]],
      email:['',[Validators.required,Validators.email]],
      tel:['',[Validators.required,Validators.pattern('[0-9]{10}')]],
      commentaire:['',Validators.max(80)],
    });
  }

  initFormNormal(){
    const now = new Date();
    const annee   = now.getFullYear();
    const mois    = now.getMonth();
    const jour    = now.getDate() + 7;

   /* if(sessionStorage.getItem("representant")){
      var annee   = now.getFullYear();
      var mois    = now.getMonth() + 1;
      var jour    = 15;
    } else {
      var annee   = now.getFullYear();
      var mois    = now.getMonth();
      var jour    = now.getDate();
    }*/

    var tmpDate = new Date(annee, mois, jour);

    let customValidatorCintre;
    if(this.cintreActif) {
      customValidatorCintre = Validators.required;
    } else {
      customValidatorCintre = Validators.nullValidator;
    }

    //si bloqPanier activé
    //this.moduleService.fDateBloqPanier().then(
      //(data:boolean)=>{
          this.dateBloqPanier=true;
          this.formCommandeNormal=this.formBuilder.group({
            date:[{value:tmpDate, disabled: this.dateBloqPanier},Validators.required],
            adresseL:['',Validators.required],
            adresseF:['',Validators.required],
            libelleServ:'',
            nom:'',
            prenom:'',
            CGVcheck:['',Validators.requiredTrue],
            cintreCheck:['0',customValidatorCintre],
            commentaire:['',Validators.max(80)]
          });
    //  });
  }

  onSubmit(){
    const resultForm=this.formCommandeColissimo.value;
    const civilite = resultForm["ceCivility"];
    const date = resultForm["date"];
    const nom=resultForm["nom"];
    const prenom=resultForm["prenom"];
    const adresse1=resultForm["adresse1"];
    const adresse2=resultForm["adresse2"];
    const cdp=resultForm["cdp"];
    const ville=resultForm["ville"];
    const email=resultForm["email"];
    const telephone=resultForm["tel"];
    const commentaire=resultForm["commentaire"];
    const pudoFOId=resultForm["scFoid"];
    const key=resultForm["scCleSHA1"];
    const trReturnUrlKo=resultForm["scURLKO"];
    const trReturnUrlok=resultForm["scURLOK"];
    const dyForwardingCharges=resultForm["scFraisExpedition"];
    const orderId=resultForm["scOrderID"];
    const numVersion=resultForm["scVersionColissimo"];
    const trClientNumber=sessionStorage.getItem("loginCompte");
    const signature=resultForm["scSignature"];
    this.commandeService.validCommande(civilite,date,nom,prenom,adresse1,adresse2,cdp,ville,email,telephone,commentaire,pudoFOId,key,trReturnUrlKo,trReturnUrlok,dyForwardingCharges,orderId,numVersion,trClientNumber,signature);

    this.isValidate=true;
  }
  onSubmitNormal(){
    const resultForm=this.formCommandeNormal.value;
    const date = resultForm["date"];
    const adresseL=resultForm["adresseL"];
    const adresseF=resultForm["adresseF"];
    const libelleServ=resultForm["libelleServ"];
    const nom=resultForm["nom"];
    const prenom=resultForm["prenom"];
    const commentaire=resultForm["commentaire"];
    const cintre=resultForm["cintreCheck"];
    this.commandeService.validCommandeNormal(date,nom,prenom,adresseL,adresseF,libelleServ,commentaire,cintre,this.totalPanierPlusFDP,this.montantPort,this.montantEscompte);
    this.isValidate=true;
  }

  goBack(stepper:MatStepper){
    stepper.previous();
  }

  onTop(){
    window.top.window.scrollTo(0,0);
  }

  noStock(taille){
    this.snackBar.open(this.pasDeStock+taille,"",{
      duration:3000
    })
  }

  /* Modification du panier en modeSaisie === 2 -->tableau */

  onChangeValueTab(idproduit:number,quantiteAvant:number,quantiteApres:number, codecolori:string, stockdispo:number = 0){
    if (quantiteApres - quantiteAvant <= stockdispo) {
      this.httpClient.post(this.httpRequest.LigneCommande,{
        "idproduit":idproduit,
        "quantite":quantiteApres,
        "codeColoris":codecolori,
        "login":sessionStorage.getItem("loginCompte")
      }).subscribe(data=>{
        this.changeArrayCommandeTab();
      });
    } else {
      //this.snackBar.open(this.quantiteInsuf,"X", {duration: 4000});
      alert(this.quantiteInsuf)
    }
  }

  onCommande(refproduit:string,libelle:string,saison:string){ //fonction permettant de détecter sur quel produit est sélectionné pour la commande expresse
    //prend en paramètre le libelle en string du produit
    this.coRefProduit="";
    this.coProduit=libelle; //coProduit prend la valeur du produit passé en paramètre de la fonction
    this.commandeExpress=true;

    this.coRefProduit=refproduit;
    this.commandeService.coRefProduit=refproduit;
    this.commandeService.emitCoRef();
  }


  changeArrayCommandeTab(){ //permet de mettre à jour l'arrayCommande dans le cas d'un modeSaisie===2
    this.arrayCommandeSubscription=this.commandeService.arrayCommandeSubject.subscribe(
      (data:any[])=>{
        this.arrayCommande=[];
        var arrayTab:any[];
        if(isDefined(data[2])){
          for(let i=0;i< Object.keys(data[2]).length;i++){
            arrayTab=[];
            arrayTab["info"]=Object.entries(data[2][i]);
            arrayTab["tableau"]=[];

            this.arrayCommande.push(arrayTab);

            if (this.imgEnCours[i] === '' || this.imgEnCours[i] === undefined) {
              for (let uneCouleur of arrayTab["info"][17][1]) {
                if (uneCouleur[0] === arrayTab["info"][18][1]) {
                  this.imgEnCours[i] = `${this.imageService.PhotosArt}/${uneCouleur[4]}`;
                  break;
                } else {
                  this.imgEnCours[i] = `${this.imageService.PhotosArt}/${arrayTab["info"][13][1]}`;
                }
              }
            }
            if(this.afficherDetails.length < Object.keys(data[2]).length) this.afficherDetails.push(this.detailsDefaut);
            /* Permet de créer le tableau des tailles commande pour le cas d'un modeSaisie===2 -->tableau */
            this.detailService.getDetail(data[2][i].refproduit).then(
              (data2:any[])=>{//récupère les détails du produit
                  // Récupère les quantités par ligne pour l'affichage du panier
                for(let k = 0; k < data2[4].length; k++) {
                  let currentTab = data2[4][k];
                  let lastElem = currentTab.length-1;
                  if(typeof(this.quantiteParLigne[i])==='undefined'){this.quantiteParLigne[i]=[]};
                  this.quantiteParLigne[i].push(currentTab[lastElem].quantiteLigne);
                }
                this.arrayTailleTableau[i]=data2[1].libcoloris;
                this.libCodeColorisArray[i]=data2[1].codeColoris;
                this.libPictoArray[i]=data2[1].pictogramme;
                this.infoTailleCommandeTableau[i]=data2[4];
                var lengthArray = this.infoTailleCommandeTableau[i].length;
                this.moduleService.showMaxQty().then(data3=>{
                  if(data3["quantiteMax"]>0){ // si quantiteMax est à 1 on cache le stock
                    for(let j=0;j<lengthArray;j++){
                      var lengthArray2=this.infoTailleCommandeTableau[i][j].length;
                      for(let k=0;k<lengthArray2;k++){
                        if(this.infoTailleCommandeTableau[i][j][k].stockdisponible>data3["valQteMax"]){
                          this.infoTailleCommandeTableau[i][j][k].stockdisponible=data3["valQteMax"];
                        }
                      }
                    }
                  }
                })
                // Permet de connaître toutes les tailles du produit
                var taille:any[]=[];
                for(let j=0;j<Object.keys(data2[4][0]).length;j++){
                  taille[j]=data2[4][0][j].taille;
                }
                this.tailleDebFin[i]=taille;
              }
            )
          }
        }
      }
    );
    this.commandeService.getCommande();
  }

  changeColori(arrayColori:any, coloriChoisi:string, index:number) {
    for(let oneColor of arrayColori) {
      if (oneColor[1] === coloriChoisi) {
        this.imgEnCours[index] = `${this.imageService.PhotosArt}/${oneColor[4]}`;
      }
    }
  }

  afficherLignePanier(commande, prixInfo, indexDetails) {
    if (this.afficherDetails[indexDetails] || prixInfo[prixInfo.length-1].quantiteLigne > 0) {
      if (prixInfo[0].tarif_promo === '0.00') {
        return Number(commande.info[2][1]) == Number(prixInfo[0].prix);
      } else {
        return Number(commande.info[2][1]) == Number(prixInfo[0].tarif_promo);
      }
    }
    return false;
  }

}
