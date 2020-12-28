import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CommandeTailleComponent } from './commande-taille.component';

describe('CommandeTailleComponent', () => {
  let component: CommandeTailleComponent;
  let fixture: ComponentFixture<CommandeTailleComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CommandeTailleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CommandeTailleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
