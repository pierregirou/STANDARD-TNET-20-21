import { Component, OnInit } from '@angular/core';
import { ModuleService } from "../../../services/modules.service";
import { AdministrationService } from "../../../services/administration.service";
import { HttpClient } from '@angular/common/http';
import { HttpRequest } from '../../../services/http-request.service';
import { Router } from '@angular/router';
import { TemplateService } from '../../../services/template.service';

@Component({
  selector: 'app-parametres-administration',
  templateUrl: './parametres-administration.component.html',
  styleUrls: ['./parametres-administration.component.css'],
})
export class ParametresAdministrationComponent implements OnInit {

  checkVisGalerie:boolean; //Récupère la valeur de visGalerie pour l'afficher dans le template
  checkMaintenance:boolean; //Récupère la valeur de maintenance pour l'afficher dans le template
  checkLangue:boolean; //Récupère la valeur de langueAng pour l'afficher dans le template
  checkUpdateAdresse:boolean; //Récupère la valeur de updateAdresse pour l'afficher dans le template
  checkSelectionDuMoment:boolean; //Récupère la valeur de la sélection du moment
  checkPromo:boolean; //récupère la valeur de promo pour afficher les promos
  checkModeSaisie:string; //récupère la valeur du mode de saisie
  checkSoColissimo:boolean; //récupère la valeur de soColissimo
  checkTimerCommande:number; //récupère la valeur du timer commande
  checkPoints:boolean; //récupère la valeur des points
  checkStockCouleur:boolean; //récupère la valeur de stockCouleur
  checkFraisDePort:boolean; //récupère la valeur des frais de port
  checkMontantPort:number; //récupère la valeur de montant port
  checkPortGratuit:number; //récupère la valeur de port gratuit
  checkStockDisponible:number; //récupère la valeur du stockDisponible
  checkStockIndisponible:number; //récupère la valeur du stockIndisponible
  checkMinStockLimite:number; // récupère la valeur de minStock
  checkMaxStockLimite:number; //récupère la valeur de maxStock
  checkControleStock:boolean; //récupère la valeur de contrôle stock
  checkQuantiteMax:boolean; //récupère la valeur de quantiteMax
  checkValQteMax:number; //récupère la valeur de la quantité max à afficher
  checkCdeMarque:boolean; //récupère la valeur de checkCdeMarque
  checkCodeTarifP:string;
  checkPourcentageT:number=0.00;
  checkPourcentageS:number=0.00;
  checkMontantS:number=0.00;
  menuSelection:any[];
  checkTexteTemplate:string; //récupère le texte du template
  checkTexteTemplateAng:string; //récupère le texte anglais du template
  bg:string; //background du template 1
  bg2:string; //background du template 2
  menuColor:string; //couleur du menu
  footerColor:string; //couleur du footer
  contenuColor:string; //couleur du contenu
  totalColor:string; //couleur du module total
  infoColor:string; //couleur du module info
  prixVenteConseille:boolean; //prix de vente connseillé
  visInformationAff:any[]=[]; // récupère dans un tableau les infos à afficher dans détail produit
  /* Dans le détailProduit affichage des infos */
  referenceD:boolean=false;
  tailleDisponibleD:boolean=false;
  colorisD:boolean=false;
  marqueD:boolean=false;
  themeD:boolean=false;
  familleD:boolean=false;
  sousFamilleD:boolean=false;
  modeleD:boolean=false;

  constructor(private templateService:TemplateService,private router:Router,private httpRequest:HttpRequest,private httpClient:HttpClient,private moduleService:ModuleService,private administrationService:AdministrationService) { 
    /* texte template obligé de le mettre dans le constructeur pour pouvoir l'afficher dans ckeditor à chaque changement d'URL */
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoTexte"
    }).subscribe(data=>{
      this.checkTexteTemplate=data[0];
    });

    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoTexteAng"
    }).subscribe(data=>{
      this.checkTexteTemplateAng=data[0];
    })

   }

   

  ngOnInit() {
    /* Statut de la maintenance */
    this.moduleService.enMaintenance().then(
      (data:boolean)=>{
        this.checkMaintenance=data;
      }
    );
    this.moduleService.infoModules().then(data=>{
      this.visInformationAff=data["visInformationAff"].split(';');
      for(let i=0;i<this.visInformationAff.length;i++){
        if(this.visInformationAff[i]==="1"){
          this.referenceD=true;
        }
        if(this.visInformationAff[i]==="2"){
          this.tailleDisponibleD=true;
        }
        if(this.visInformationAff[i]==="3"){
          this.colorisD=true;
        }
        if(this.visInformationAff[i]==="4"){
          this.marqueD=true;
        }
        if(this.visInformationAff[i]==="5"){
          this.themeD=true;
        }
        if(this.visInformationAff[i]==="6"){
          this.familleD=true;
        }
        if(this.visInformationAff[i]==="7"){
          this.sousFamilleD=true;
        }
        if(this.visInformationAff[i]==="8"){
          this.modeleD=true;
        }
      }
      /* statut de visGalerie */
      if(data["visGalerie"]==1){
        this.checkVisGalerie=true;
      }else{
        this.checkVisGalerie=false;
      }
      /* statut de maintenance */
      if(data["maintenance"]==1){
        this.checkMaintenance=true;
      }else{
        this.checkMaintenance=false;
      }
      /* Statut langue */
      if(data["langueAng"]==1){
        this.checkLangue=true;
      }else{
        this.checkLangue=false;
      }
      /* Statut update adresse */
      if(data["updateAdresse"]==1){
        this.checkUpdateAdresse=true;
      }else{
        this.checkUpdateAdresse=false;
      }
      /* Statut de la sélection du moment */
      if(data["selectionMoment"]==1){
        this.checkSelectionDuMoment=true;
      }else{
        this.checkSelectionDuMoment=false;
      }
      /* Statut de promotion */
      if(data["promotion"]==1){
        this.checkPromo=true;
      }else{
        this.checkPromo=false;
      }
      /* Mode saisie */
      this.checkModeSaisie=data["modeSaisie"];
      /* SoColissimo */
      if(data["soColissimo"]==1){
        this.checkSoColissimo=true;
      }else{
        this.checkSoColissimo=false;
      }
      /* timer commande */
      this.checkTimerCommande=(data["timerCommande"]/60);
      /* CodeTarif Promo */
      this.checkCodeTarifP=data["promoCodeTarif"];
      /* Pourcentage tarif Promo */
      this.checkPourcentageT=data["promoPourcentageCodeTarif"];
      /* Pourcentage sur tous le site */
      this.checkPourcentageS=data["promoPourcentage"];
      /* Montant sur tous le site */
      this.checkMontantS=data["promoMontant"];
      /* Points */
      if(data["points"]==1){
        this.checkPoints=true;
      }else{
        this.checkPoints=false;
      }
      /* Stock couleur */
      if(data["stockCouleur"]==1){
        this.checkStockCouleur=true;
      }else{
        this.checkStockCouleur=false;
      }
      /* Frais de port */
      if(data["fraisDePort"]==1){
        this.checkFraisDePort=true;
      }else{
        this.checkFraisDePort=false;
      }
      /* montant port */
      this.checkMontantPort=data["montantPort"];
      /* Port gratuit */
      this.checkPortGratuit=data["portGratuit"];
      /* stock disponible */
      this.checkStockDisponible=data["stockDisponible"];
      /* stock indisponible */
      this.checkStockIndisponible=data["stockIndisponible"];
      /* minStockLimite */
      this.checkMinStockLimite=data["minStockLimite"];
      /* maxStockLimite */
      this.checkMaxStockLimite=data["maxStockLimite"];
      /* prixVenteConseille */
      if(data["prixVenteConseille"]==1){
        this.prixVenteConseille=true;
      }else{
        this.prixVenteConseille=false;
      }
      /* Statut du controleStock */
      if(data["controleStock"]==1){
        this.checkControleStock=true;
      }else{
        this.checkControleStock=false;
      }
      /* Statut de quantieMax */
      if(data["quantiteMax"]==1){
        this.checkQuantiteMax=true;
      }else{
        this.checkQuantiteMax=false;
      }
      /* ValQteMax */
      this.checkValQteMax=data["valQteMax"];
      /* Statut de cdeMarque */
      if(data["cdeMarque"]==1){
        this.checkCdeMarque=true;
      }else{
        this.checkCdeMarque=false;
      }
      /* info menu */
      this.httpClient.post(this.httpRequest.MenuInfo,{
        "choix":"info"
      }).subscribe(data=>{
        this.menuSelection=Object.entries(data);
      })
    
    });
    /* Récupère la couleur de background1 depuis template */
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoBG"
    }).subscribe(data=>{
      this.bg='#'+data[0];
    });
    /* Récupère la couleur du background2 depuis template */
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoBG2"
    }).subscribe(data=>{
      this.bg2='#'+data[0];
    });
    /* Récupère la couleur du menu depuis template */
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoMenuColor"
    }).subscribe(data=>{
      this.menuColor='#'+data[0];
    });
    /* Récupère la couleur du footer depuis template */
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoFooterColor"
    }).subscribe(data=>{
      this.footerColor='#'+data[0];
    });
    /* Récupère la couleur du contenu depuis template */
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoContenuColor"
    }).subscribe(data=>{
      this.contenuColor='#'+data[0];
    });
    /* Récupère la couleur du module total depuis template */
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoTotalColor"
    }).subscribe(data=>{
      this.totalColor='#'+data[0];
    });
    /* Récupère la couleur du module info depuis template */
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"infoInfoColor"
    }).subscribe(data=>{
      this.infoColor='#'+data[0];
    });
  }

  /* Change le statut de visGalerie */
  visGalerieChange(){
    var valeur:number;
    if(this.checkVisGalerie){
      this.checkVisGalerie=false;
      valeur=0;
    }else{
      this.checkVisGalerie=true;
      valeur=1;
    }
    this.administrationService.updateModule("visGalerie",String(valeur));
  }

  /* Change le statut de maintenance */
  maintenanceChange(){
    var valeur:number;
    if(this.checkMaintenance){
      this.checkMaintenance=false;
      valeur=0;
    }else{
      this.checkMaintenance=true;
      valeur=1;
    }
    this.administrationService.updateModule("maintenance",String(valeur));
  }

  /* Change la valeur de langueAng */
  langueChange(){
    var valeur:number;
    if(this.checkLangue){
      this.checkLangue=false;
      valeur=0;
    }else{
      this.checkLangue=true;
      valeur=1;
    }
    this.administrationService.updateModule("langueAng",String(valeur));
  }

  /* Change la valeur de updateAdresse */
  updateAdresseChange(){
    var valeur:number;
    if(this.checkUpdateAdresse){
      this.checkUpdateAdresse=false;
      valeur=0;
    }else{
      this.checkUpdateAdresse=true;
      valeur=1;
    }
    this.administrationService.updateModule("updateAdresse",String(valeur));
  }

  /* change la valeur de prixVenteConseille */
  prixVenteConseilleChange(){
    var valeur:number;
    if(this.prixVenteConseille){
      this.prixVenteConseille=false;
      valeur=0;
    }else{
      this.prixVenteConseille=true;
      valeur=1;
    }
    this.administrationService.updateModule("prixVenteConseille",String(valeur));
  }

  /* Change la valeur de selectionMoment */
  selectionMomentChange(){
    var valeur:number;
    if(this.checkSelectionDuMoment){
      this.checkSelectionDuMoment=false;
      valeur=0;
    }else{
      this.checkSelectionDuMoment=true;
      valeur=1;
    }
    this.administrationService.updateModule("selectionMoment",String(valeur));
  }

  /* Change la valeur de promotion */
  promotionChange(){
    var valeur:number;
    if(this.checkPromo){
      this.checkPromo=false;
      valeur=0;
    }else{
      this.checkPromo=true;
      valeur=1;
    }
    this.administrationService.updateModule("promotion",String(valeur));
  }

  /* Change la valeur de modeSaisie */
  modeSaisieChange(){
    this.administrationService.updateModule("modeSaisie",this.checkModeSaisie);
  }

  /* Change la valeur de soColissimo */
  soColissimoChange(){
    var valeur:number;
    if(this.checkSoColissimo){
      this.checkSoColissimo=false;
      valeur=0;
    }else{
      this.checkSoColissimo=true;
      valeur=1;
    }
    this.administrationService.updateModule("soColissimo",String(valeur));
  }

  /* Change la valeur du timer commande */
  timerCommandeChange(){
    var timerSec=(this.checkTimerCommande*60);
    this.administrationService.updateModule("timerCommande",String(timerSec));
  }

  /* Change le code tarif */
  codeTarifPromoChange(){
    this.administrationService.updateModule("promoCodeTarif",String(this.checkCodeTarifP));
  }

  /* Change la valeur du pourcentage à appliquer au code Tarif */
  PourcentagePromoChange(){    
    this.administrationService.updateModule("promoPourcentageCodeTarif",String(this.checkPourcentageT));
  }

  /* Change la valeur du pourcentage à appliquer au site  */
  pourcentageSPromoChange(){    
    this.administrationService.updateModule("promoPourcentage",String(this.checkPourcentageS));
  }

  /* Change la valeur du montant à appliquer au site */
  montantPromoChange(){    
    this.administrationService.updateModule("promoMontant",String(this.checkMontantS));
  }


  /* Change la valeur pour activer les points */
  pointsActivateChange(){
    var valeur:number;
    if(this.checkPoints){
      this.checkPoints=false;
      valeur=0;
    }else{
      this.checkPoints=true;
      valeur=1;
    }
    this.administrationService.updateModule("points",String(valeur));
  }

  /* Change la valeur pour activer stockCouleur */
  stockCouleurChange(){
    var valeur:number;
    if(this.checkStockCouleur){
      this.checkStockCouleur=false;
      valeur=0;
    }else{
      this.checkStockCouleur=true;
      valeur=1;
    }
    this.administrationService.updateModule("stockCouleur",String(valeur));
  }


  /* Change la valeur des frais de port */
  fraisDePortChange(){
    var valeur:number;
    if(this.checkFraisDePort){
      this.checkFraisDePort=false;
      valeur=0;
    }else{
      this.checkFraisDePort=true;
      valeur=1;
    }
    this.administrationService.updateModule("fraisDePort",String(valeur));
  }

  /* Change la valeur du montant des frais de port */
  montantPortChange(){
    this.administrationService.updateModule("montantPort",String(this.checkMontantPort));
  }

  /* Change la valeur de port gratuit */
  portGratuitChange(){
    this.administrationService.updateModule("portGratuit",String(this.checkPortGratuit));
  }

  /* Change la valeur de stockDisponible */
  stockDisponibleChange(){
    this.administrationService.updateModule("stockDisponible",String(this.checkStockDisponible));
  }

  /* Change la valeur de stockIndisponible */
  stockIndisponibleChange(){
    this.administrationService.updateModule("stockIndisponible",String(this.checkStockIndisponible));
  }

  /* Change la valeur de minStockLimite */
  minStockLimiteChange(){
    this.administrationService.updateModule("minStockLimite",String(this.checkMinStockLimite));
  }

  /* Change la valeur de maxStockLimite */
  maxStockLimiteChange(){
    this.administrationService.updateModule("maxStockLimite",String(this.checkMaxStockLimite));
  }

  /* Change la valeur de controleStock */
  controleStockChange(){
    var valeur:number;
    if(this.checkControleStock){
      this.checkControleStock=false;
      valeur=0;
    }else{
      this.checkControleStock=true;
      valeur=1;
    }
    this.administrationService.updateModule("controleStock",String(valeur));
  }

  /* Change la valeur de quantiteMax */
  quantiteMaxChange(){
    var valeur:number;
    if(this.checkQuantiteMax){
      this.checkQuantiteMax=false;
      valeur=0;
    }else{
      this.checkQuantiteMax=true;
      valeur=1;
    }
    this.administrationService.updateModule("quantiteMax",String(valeur));
  }

  /* Change la valeur de valQteMax */
  valQTeMaxChange(){
    this.administrationService.updateModule("valQteMax",String(this.checkValQteMax));
  }

  /* Change la valeur de checkCdeMarque */
  cdeMarqueChange(){
    var valeur:number;
    if(this.checkCdeMarque){
      this.checkCdeMarque=false;
      valeur=0;
    }else{
      this.checkCdeMarque=true;
      valeur=1;
    }
    this.administrationService.updateModule("cdeMarque",String(valeur));
  }

  getInfoActifMenu(actif:string){
    if(actif==="1"){
      return true;
    }else{
      return false;
    }
  }

  actifMenuChange(id:string, actif:string,ordreMenu:string){
    var actifToChange:string;
    if(actif==="1"){
      actifToChange="0";
    }
    if(actif==="0"){
      actifToChange="1"
    }
    this.httpClient.post(this.httpRequest.MenuInfo,{
      "choix":"update",
      "idToUpdate":id,
      "ordreMenu":ordreMenu
    }).subscribe(data=>{
      this.ngOnInit();
    })
  }

  ordreMenuChange(){
    this.ngOnInit();
  }

  texteTemplateChange(){
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"updateTexte",
      "texteToUpdate":this.checkTexteTemplate
    }).subscribe();
  }

  texteAngTemplateChange(){
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"updateTexteAng",
      "texteAngToUpdate":this.checkTexteTemplateAng
    }).subscribe();
  }


  /* Change la valeur du backgroundcolor 1 */
  backgroundColorChange(value){
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"updateBG",
      "bgToUpdate":value.substring(1)
    }).subscribe(data=>{
      this.templateService.getBackgroundColor();
      this.templateService.emitBackGroundColor();
    });
  }
  /* Change la valeur du backgroundcolor 2 */
  backgroundColorChange2(value){
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"updateBG2",
      "bg2ToUpdate":value.substring(1)
    }).subscribe();
  }
  /* Change la valeur de menuColor */
  menuColorChange(value){
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"updateMenuColor",
      "menuColorToUpdate":value.substring(1)
    }).subscribe(data=>{
      this.templateService.getMenuColor();
      this.templateService.emitMenuColor();
    });
  }

  /* Change la valeur de footerColor */
  footerColorChange(value){
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"updateFooterColor",
      "footerColorToUpdate":value.substring(1)
    }).subscribe(data=>{
      this.templateService.getFooterColor();
      this.templateService.emitFooterColor();
    });
  }

  /* Change la valeur de contenuColor */
  contenuColorChange(value){
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"updateContenuColor",
      "contenuColorToUpdate":value.substring(1)
    }).subscribe();
  }

  /* Change la valeur de totalColor */
  totalColorChange(value){
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"updateTotalColor",
      "totalColorToUpdate":value.substring(1)
    }).subscribe();
  }

  /* Change la valeur de infoColor */
  infoColorChange(value){
    this.httpClient.post(this.httpRequest.InfoTemplate,{
      "choix":"updateInfoColor",
      "infoColorToUpdate":value.substring(1)
    }).subscribe();
  }

  

  /* Change visInformationAff */
  visInformationChange(choix:string){
    var visInformationAffToupdate:any[]=[];
    if(choix==='1'){

      if(this.referenceD===true){
        this.referenceD=false;
      }else{
        this.referenceD=true;
      }
    }
    if(choix==='2'){
      if(this.tailleDisponibleD===true){
        this.tailleDisponibleD=false;
      }else{
        this.tailleDisponibleD=true;
      }
    }
    if(choix==='3'){
      if(this.colorisD===true){
        this.colorisD=false;
      }else{
        this.colorisD=true;
      }
    }
    if(choix==='4'){

      if(this.marqueD===true){
        this.marqueD=false;
      }else{
        this.marqueD=true;
      }
    }
    if(choix==='5'){

      if(this.themeD===true){
        this.themeD=false;
      }else{
        this.themeD=true;
      }
    }
    if(choix==='6'){

      if(this.familleD===true){
        this.familleD=false;
      }else{
        this.familleD=true;
      }
    }
    if(choix==='7'){

      if(this.sousFamilleD===true){
        this.sousFamilleD=false;
      }else{
        this.sousFamilleD=true;
      }
    }
    if(choix==='8'){

      if(this.modeleD===true){
        this.modeleD=false;
      }else{
        this.modeleD=true;
      }
    }


    if(this.referenceD){
      visInformationAffToupdate.push("1");
    }
    if(this.tailleDisponibleD){
      visInformationAffToupdate.push("2");
    }
    if(this.colorisD){
      visInformationAffToupdate.push("3");
    }
    if(this.marqueD){
      visInformationAffToupdate.push("4");
    }
    if(this.themeD){
      visInformationAffToupdate.push("5");
    }
    if(this.familleD){
      visInformationAffToupdate.push("6");
    }
    if(this.sousFamilleD){
      visInformationAffToupdate.push("7");
    }
    if(this.modeleD){
      visInformationAffToupdate.push("8");
    }

    var vis=visInformationAffToupdate.join(';');
    this.httpClient.post(this.httpRequest.Administration,{
      "module":"visInf",
      "valeur":vis
    }).subscribe();
  }
}
