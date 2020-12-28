import { Component, OnInit } from '@angular/core';
import { TelechargementService } from '../../services/telechargement.service';
import { Telechargement } from '../../models/telechargement.models';
import { TemplateService } from '../../services/template.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-contenu-telechargements',
  templateUrl: './contenu-telechargements.component.html',
  styleUrls: ['./contenu-telechargements.component.css']
})
export class ContenuTelechargementsComponent implements OnInit {
  telechargement:Telechargement[]=[];
  contenuColorSubscription:Subscription;
  contenuColor:String;

  constructor(private templateService:TemplateService,private telechargementService:TelechargementService) { }

  ngOnInit() {
    this.templateService.getContenuColor();
    this.contenuColorSubscription=this.templateService.contenuColorSubject.subscribe(
      (contenuColor:string)=>{
        this.contenuColor='#'+contenuColor;
      }
    );
    this.templateService.emitContenuColor();
    this.telechargementService.getTelechargement().then(
      (data)=>{
        this.telechargement = [];
        var keys = Object.keys(data);
        for(let i=1; i<keys.length;i++){
          this.telechargement.push(new Telechargement(data[i].idTelechargement, data[i].intitule,data[i].type,data[i].lien));
        }
      }
    );
  }

}
