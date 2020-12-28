import { Component, OnInit, EventEmitter, Output} from '@angular/core';
import { ImageService } from '../../services/images.service';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import { Telechargement } from '../../models/telechargement.models';
import { TelechargementService } from '../../services/telechargement.service';

@Component({
  selector: 'app-menu-representant',
  templateUrl: './menu-representant.component.html',
  styleUrls: ['./menu-representant.component.css']
})
export class MenuRepresentantComponent implements OnInit {
  @Output() onChangeMode = new EventEmitter();
  telechargement:Telechargement[]=[];
  currentMode:string=''

  constructor(private router:Router,public imageService:ImageService, private authService:AuthService, private telechargementService:TelechargementService) { }

  ngOnInit() {
    this.currentMode='liste'
    this.telechargementService.getTelechargement().then(
      (data)=>{
        var keys = Object.keys(data);
        var j = 0
        for(let i=1; i<keys.length;i++){
          this.telechargement[j] = new Telechargement(data[i].idTelechargement, data[i].intitule,data[i].type,data[i].lien);
          j++;
        }
      }
    );
  }

  afficherHistory(){
    this.currentMode = 'commande';
    this.onChangeMode.emit('commande');
  }

  afficherListe(){
    this.currentMode = 'liste';
    this.onChangeMode.emit('liste');
  }

  deconnexion(){
    this.authService.logOut();
    this.router.navigate(['']);
  }

}
