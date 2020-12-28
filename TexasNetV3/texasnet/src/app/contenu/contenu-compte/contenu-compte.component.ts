import { Component, OnInit, Input } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { NgForm, Validators } from '@angular/forms';
import { InformationService } from '../../services/informations.service';
import { FormBuilder, FormGroup } from '@angular/forms';
import { Router } from '@angular/router';
import { MatStepper } from '@angular/material/stepper'
import { MatSnackBar } from '@angular/material'; //SnackBar
import { ModuleService } from '../../services/modules.service';
import { HttpRequest } from '../../services/http-request.service';
import { CommandeService } from '../../services/commandes.service';
import { detailLigneCde } from '../../models/detailLigneCde.models';
import { TemplateService } from '../../services/template.service';
import { Subscription } from 'rxjs';
import * as sha1 from 'js-sha1';
import { ImageService } from 'src/app/services/images.service';
import { TranslateService,LangChangeEvent } from '@ngx-translate/core';
import { BreakpointObserver } from '@angular/cdk/layout';

@Component({
  selector: 'app-contenu-compte',
  templateUrl: './contenu-compte.component.html',
  styleUrls: ['./contenu-compte.component.css']
})
export class ContenuCompteComponent implements OnInit {
  @Input() menuSnapAction:string;
  hide:boolean=true //variable permettant d'afficher mot de passe en clair
  PassForm:FormGroup;
  ChangeForm:FormGroup;
  firstFormControl:FormGroup;
  isOptional:boolean = false; //bloque l'accès à l'utilisateur d'accèder au changement de mot de passe si mdp invalide
  isChange:boolean = false //indique si le mot de passe a bien été changé
  isEditable:boolean = false; //bloque l'accès à l'utilisateur de revnir en arrière
  changePassword:boolean=false; //variable verifiant le mot de passe du client si il est valide ou non
  changeState:boolean=false; //variable permettant de savoir si le mot de passe a bien été changé;
  detailLigne:detailLigneCde[]=[];
  //Variables pour les coordonnées
  nom:string;
  prenom:string;
  email:string;
  adresse1:string;
  adresse2:string;
  cp:string;
  ville:string;
  telephone:string;
  langue:string;
  nomUpdate:string;
  prenomUpdate:string;
  emailUpdate:string;
  adresse1Update:string;
  adresse2Update:string;
  cpUpdate:string;
  villeUpdate:string;
  telephoneUpdate:string;
  langueUpdate:string;
  arrayHistorique:any[];
  /************************ */
  messageErreurAuth:string; //affiche un message d'erreur si le mot de passe saisi n'est pas correct
  messageErreurChange:string; //affiche un message d'erreur si les deux mots de passe ne correpondent pas
  newMDP:string=""; //ngModel verifie si le champ est rempli
  confirmMDP:string=""; //ngModel permet de comparer le mot de nouveau mdp et la confirmation

  updateAdresse:boolean; //booleenne si true l'utilisateur peut modifier ses coordonnées sinon il peut seulement les visualiser
  bloqueModifParams:boolean; // bloque la possibilité de modifier le mot de passe et les coordonnées de l'utilisateur
  scRetour:string; //vérifier si le retour est activé ou pas
  scRetourB:boolean; //active le bouton
  retourCommandeArray:any[]=[];

  pudoFOId:string;
  key:string;
  dyForwardingCharges:string;
  orderId:string;
  numVersion:string;
  trClientNumber:string;
  signature:string;

  contenuColor:string;
  contenuColorSubscription:Subscription;
  msgUpdate:string

  affichageMobile:boolean = false;

  constructor(private templateService:TemplateService,private httpRequest:HttpRequest, private BreakpointObserver: BreakpointObserver,private httpClient:HttpClient,private informationService:InformationService,private formBuilder:FormBuilder,private router:Router,private snackBar:MatSnackBar,private moduleService:ModuleService,private commandeService:CommandeService,private imageService:ImageService,translate: TranslateService) {

    BreakpointObserver.observe([
      '(max-width: 600px)'
    ]).subscribe(result => {
      if (result.matches) {
        this.affichageMobile = false;
      }else{
        this.affichageMobile = true;
      }
    });

    translate.get('compte.miseAJour').subscribe((res: string) => {
      this.msgUpdate = res;
    });

    translate.onLangChange.subscribe((event: LangChangeEvent) => {
      translate.get('compte.miseAJour').subscribe((res: string) => {
        this.msgUpdate = res;
      });
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
      this.langue=data["langue"];

      this.moduleService.scRetour().then(
        (data)=>{
          this.scRetour = data["scRetour"];

          if (this.scRetour === '1') {
            this.scRetourB = true;
          } else {
            this.scRetourB = false;
          }
        })
    });

    this.httpClient.post(this.httpRequest.HistoriqueCommande,{
      "login":sessionStorage.getItem("loginCompte")
    }).subscribe(
      (data:any[])=>{
        this.arrayHistorique=data;
      }
    )
   }

   goBack(stepper : MatStepper){
     stepper.previous();
   }

   goFoward(stepper: MatStepper){
     stepper.next();
   }


  ngOnInit() {
    this.initForm();
    this.initForm2();
    this.initRetourCommandeForm();
    this.moduleService.updateAdresseStatus().then(
      (status)=>{
        if(status){
          this.updateAdresse=true;
        }else{
          this.updateAdresse=false;
        }
      }
    );
    this.moduleService.bloqueModifParamStatus().then(
      (status)=>{
        if(status){
          this.bloqueModifParams=true;
        }else{
          this.bloqueModifParams=false;
        }
        this.bloqueModifParams=true;
      }
    );
    this.templateService.getContenuColor();
    this.contenuColorSubscription=this.templateService.contenuColorSubject.subscribe(
      (contenuColor:string)=>{
        this.contenuColor='#'+contenuColor;
      }
    );
    this.templateService.emitContenuColor();

    this.moduleService.infoModules().then(data=>{
      this.pudoFOId=data["scFoid"];
      this.key=data["scCleSHA1"];
      this.dyForwardingCharges=data["scFraisExpedition"];
      this.numVersion=data["scVersionColissimo"];
      this.trClientNumber=sessionStorage.getItem("loginCompte");
      this.signature="";
    })

  }

  initForm(){
    this.PassForm=this.formBuilder.group({
      mdp:["",[Validators.required]]
    })
  }

  initForm2(){
    this.ChangeForm=this.formBuilder.group({
      newMDP:["",[Validators.required,Validators.minLength(6)]],
      confirmMDP:["",[Validators.required,Validators.minLength(6)]]
    })
  }

  initRetourCommandeForm(){

  }

  onSubmit(form:NgForm){

    //mise à jour du choix de la langue
    if(form.value["langue"]!=""){
      this.langue=form.value["langue"];
    }
    this.informationService.updateUser(
      sessionStorage.getItem("loginCompte"),
      form.value["nom"],
      form.value["prenom"],
      form.value["email"],
      form.value["adresse1"],
      form.value["adresse2"],
      form.value["cp"],
      form.value["ville"],
      form.value["telephone"],
      form.value["langue"]
    ).then(
      (value)=>{ //si true affiche le snackBar Mise à jour réussie
        this.snackBar.open(this.msgUpdate,"Réussie",{
          duration:3000,
        });
      }
    )
  }

  onSubmitPassword(form:NgForm){
    this.informationService.passwordVerify(sessionStorage.getItem("loginCompte"),form.value["mdp"]).then(
      (value)=>{
        if(value){
          this.changePassword=true;
        }else{
         this.messageErreurAuth="Le mot de passe saisi ne correpond pas au client "+sessionStorage.getItem("loginCompte");
        }
      }
    );
    return false;
  }

  onPassSubmit(stepper : MatStepper){
    const pass=this.PassForm.get("mdp").value;
    this.informationService.passwordVerify(sessionStorage.getItem("loginCompte"),pass).then(
      (value)=>{
        if(value){
          this.isOptional=true;
          stepper.next();
        }else{
          this.isOptional=false;
          this.messageErreurAuth="Le mot de passe saisi ne correspond pas au client "+sessionStorage.getItem("loginCompte");
          stepper.previous();
        }
      }
    )
  }
onChangePasswordForm(form:NgForm){
    if(form.value["newMDP"]!=form.value["confirmMDP"]){
    }else{
      this.informationService.changePassword(form.value["confirmMDP"],sessionStorage.getItem("loginCompte"));
    }
  }

  onSubmitChange(stepper:MatStepper){
    const newMDP = this.ChangeForm.get("newMDP").value;
    const confirmMDP = this.ChangeForm.get("confirmMDP").value;
    if(newMDP!=confirmMDP){
      this.messageErreurChange="Les deux mots de passe saisis sont différents";
      stepper.previous();
      this.isChange=false;
    }else if(newMDP==confirmMDP){
      this.messageErreurChange="";
      this.informationService.changePassword(confirmMDP,sessionStorage.getItem("loginCompte"));
      this.isChange=true
      stepper.next();
      this.changeState=true; //passe à true et affiche un message de succès si le changement s'est bien passé
      setTimeout( // revient automatiquement à l'accueil au bout de 10 secondes
        ()=>{
          this.router.navigate(['/contenu/accueil']);
        },10000
      );
    }
  }

  openSnackBar(){
    this.snackBar.open(this.msgUpdate,"Dance",{
      duration:2000,
    });
  }

  onCommande(numCommande){
    this.httpClient.post(this.httpRequest.InfoLigneCommande,{
        "login":sessionStorage.getItem("loginCompte"),
        "numCommande":numCommande,
    }).subscribe(data=>{
      var keys = Object.entries(data);
      this.detailLigne=[];
      for (let i=0;i<keys.length;i++){
        this.detailLigne[i]=new detailLigneCde(keys[i][0],Object.entries(keys[i][1]));
      }
    });
  }

  onChangeForm(refProduit:string,quantiteR:number,numCommande:string,idDetailProduit:number){
    var p = 0;
    if(this.retourCommandeArray.length > 0){
      for (let i=0;i<this.retourCommandeArray.length; i++){
        if(this.retourCommandeArray[i][0].refProduit !== refProduit){
          p++;
         } else {
          this.retourCommandeArray[i][0].quantiteR = quantiteR;
         }
      }
       if(p === this.retourCommandeArray.length){
        this.retourCommandeArray.push([{"numCommande":numCommande,"idDetailProduit":idDetailProduit,"refProduit":refProduit,"quantiteR":quantiteR}]);
       }
    }   else {
      this.retourCommandeArray.push([{"numCommande":numCommande,"idDetailProduit":idDetailProduit,"refProduit":refProduit,"quantiteR":quantiteR}]);
    }
  }

  onSubmitForm(){
    var keys = this.retourCommandeArray;

    this.httpClient.post(this.httpRequest.AddRetour,{
      "login":sessionStorage.getItem("loginCompte"),
      "keys":keys
    }). subscribe();

    this.httpClient.post(this.httpRequest.AddLigneRetour,{
      "login":sessionStorage.getItem("loginCompte"),
      "keys":keys
    }). subscribe(data=>{
        var numCommande = data[0][0][0].numCommande;
        this.sendColissimoRetour(numCommande)
    })
  }

  sendColissimoRetour(numCommande){
    this.orderId=numCommande;
      var infoClient = JSON.parse(sessionStorage.getItem("infoClient"));

      var dataCrypt = "05462952435457";
      dataCrypt += "Inwitex";
      dataCrypt += "2";
      dataCrypt += "false";
      dataCrypt += sessionStorage.getItem("loginCompte");
      dataCrypt += "";
      dataCrypt += this.orderId;
      dataCrypt += "123456";
      dataCrypt = sha1(dataCrypt);

      this.commandeService.validRetour(infoClient.civilite,infoClient.raisonSocialeFact,infoClient.complementFacturation,infoClient.raisonSociale,infoClient.complementLivraison,infoClient.codePostalFact,infoClient.villeFact,infoClient.email,infoClient.telephone,this.pudoFOId,this.orderId,dataCrypt)

  }


}
