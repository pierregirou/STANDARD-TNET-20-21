/* component visu-site ==> permet à une personne non loggué de visualiser le site */
import { Component, OnInit } from '@angular/core';
import { ModuleService } from '../services/modules.service';
import { Router } from '@angular/router';
import { ActivatedRoute } from '@angular/router';
import { MatSnackBar } from '@angular/material';
import { TranslateService, LangChangeEvent } from '@ngx-translate/core';
@Component({
  selector: 'app-visu-site',
  templateUrl: './visu-site.component.html',
  styleUrls: ['./visu-site.component.css']
})
export class VisuSiteComponent implements OnInit {
  type: string // permet de naviguer entre l'accueil et les produits pour visualiser la galerie si l'utilisateur en est autorisé
  maintenance: boolean;
  afficherFiltres: boolean;
  connexionObligatoire: string;

  constructor( private moduleService: ModuleService,
               private router: Router,
               private route: ActivatedRoute,
               private snackBar: MatSnackBar,
               translate: TranslateService) {


  translate.get('connexion.obligatoire').subscribe((res: string) => {
    this.connexionObligatoire = res;
  });

  translate.onLangChange.subscribe((event: LangChangeEvent) => {
    translate.get('connexion.obligatoire').subscribe((res: string) => {
      this.connexionObligatoire = res;
    });
  });

  /*http://accueil/:type =>récupère le type*/
  route.params.subscribe(
    (value) => {
      this.type = value.type;
      // si l'utilisateur souhaite accéder à la galerie et que le module visGalerie est flse redirige vers connexion
      if (value.type === 'produits') {
      // Seulement l'accueil est accessible
          this.moduleService.visGalerieStatus().then(
            (status) => {
              if (!status) {
                this.router.navigate(['/connexion']);  // si visGalerie=false redirige vers la connexion
                this.snackBar.open(this.connexionObligatoire, '', {duration: 3000}); // affiche un snackbar si visGalerie=false
              }
            }
          );
        }
      }
    );
  }

  ngOnInit() {
    this.moduleService.enMaintenance().then(
      (data:boolean)=>{
        this.maintenance=data;
      })

  }

}
