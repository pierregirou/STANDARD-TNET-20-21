import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ParametresAdministrationComponent } from './parametres-administration.component';

describe('ParametresAdministrationComponent', () => {
  let component: ParametresAdministrationComponent;
  let fixture: ComponentFixture<ParametresAdministrationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ParametresAdministrationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ParametresAdministrationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
