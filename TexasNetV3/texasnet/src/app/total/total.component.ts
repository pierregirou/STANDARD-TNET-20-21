import { Component, OnInit, OnDestroy } from '@angular/core';
import { Subscription } from 'rxjs';
import { CommandeService } from '../services/commandes.service';
import { Router } from '@angular/router';
import { ModuleService } from '../services/modules.service';
import { isDefined } from '@angular/compiler/src/util';
import { TemplateService } from '../services/template.service';

@Component({
  selector: 'app-total',
  templateUrl: './total.component.html',
  styleUrls: ['./total.component.css']
})
export class TotalComponent implements OnInit, OnDestroy {
  arrayCommandeSubscription:Subscription;
  montant:number=0;
  pieces:number=0;
  modeSaisie:number;
  isDisabled:boolean=false; // permet d'activer ou de désactiver le bouton total
  arrayCommande:any[]=[];
  totalColor:string;
  articleExiste:boolean = false;
  totalColorSubscription:Subscription;
  constructor(private templateService:TemplateService,private moduleService:ModuleService, private commandeService:CommandeService, private router:Router) {
    this.commandeService.getCommande();
  }
  ngOnInit() {
    this.templateService.getTotalColor();
    this.totalColorSubscription=this.templateService.totalColorSubject.subscribe(
      (contenuColor:string)=>{
        this.totalColor='#'+this.totalColor;
      }
    );
    this.templateService.emitTotalColor();
    this.moduleService.modeSaisie().then(
      (modeSaisie:number)=>{
        this.modeSaisie=Number(modeSaisie);
      }
    );
    /* Si l'utilisateur est connecté affiche dans le total le montant et le nombre de pièces de sa commande */
    if((sessionStorage.getItem("isLoggedIn")!==null && sessionStorage.getItem("isLoggedIn")==="true")){
      this.arrayCommandeSubscription=this.commandeService.arrayCommandeSubject.subscribe( //récupère l'array des produits en commande depuis COmmandeService
        (data:any[])=>{
          if(isDefined(data[1])){ //si est définie
            if(this.montant<1){
              this.montant=data[1].montant;
            }else{
              this.montant=this.commandeService.montant;
            }
            this.pieces=this.commandeService.pieces;
            this.pieces > 0 ? this.articleExiste = true : this.articleExiste = false;
            this.arrayCommande=data[2]; //array contenant les produits en commande pour les afficher dans le template
            this.isDisabled=this.commandeService.isDisabled;
          }
        }
      )
      this.commandeService.emitCommande();
    }else{
      /* Sinon mets ces valeurs à 0 */
      this.montant=0;
      this.pieces=0;
      this.articleExiste=false;
      this.isDisabled=false;
    }
  }

  buttonCommande(){
    this.router.navigate(['/contenu/panier']);
  }

  ngOnDestroy(){
    if((sessionStorage.getItem("isLoggedIn")!==null)){
      this.arrayCommandeSubscription.unsubscribe();
    }
  }

}
